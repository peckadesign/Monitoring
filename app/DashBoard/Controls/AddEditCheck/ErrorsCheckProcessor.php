<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class ErrorsCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	private const INPUT_MUTED_ERRORS_JSON = 'mutedErrorsJson';

	/**
	 * @param \Pd\Monitoring\Check\ErrorsCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->mutedErrorsJson = $data[self::INPUT_MUTED_ERRORS_JSON];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\ErrorsCheck();
	}


	/**
	 * @param \Pd\Monitoring\Check\NumberValueCheck $check
	 */
	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$url = \Pd\Monitoring\DashBoard\Forms\Controls\UrlControlFactory::create();
		$url->setOption('description', 'URL musí vracet HTTP stavový kód 200 a musí obsahovat seznam skalárních hodnot ve formátu JSON např. [10,13]');
		$form->addComponent($url, 'url');

		$mutedErrorsJsonInput = $form->addTextArea(self::INPUT_MUTED_ERRORS_JSON, 'Ignorované chyby');
		$mutedErrorsJsonInput->setOption('description', 'Seznam skalarních hodnot ve formátu JSON např. [10,13]. Tyto položky budou při kontrole ignorovány.');
		$mutedErrorsJsonInput->setRequired(FALSE);
		$mutedErrorsJsonInput->setNullable();
		$mutedErrorsJsonInput->addRule(static function (\Nette\Forms\IControl $control): bool {
			/** @var string|null $value */
			$value = $control->getValue();

			if ($value === NULL) {
				return FALSE;
			}

			return self::validateJsonList($value);
		}, 'Musí být validní json (seznam skalárních hodnot)');
	}


	public static function validateJsonList(
		string $json
	): bool
	{
		try {
			$list = \Nette\Utils\Json::decode($json, \Nette\Utils\Json::FORCE_ARRAY);

			return
				\Nette\Utils\Arrays::isList($list)
				&& \Nette\Utils\Validators::everyIs($list, 'scalar')
			;
		} catch (\Nette\Utils\JsonException $e) {
			return FALSE;
		}
	}

}
