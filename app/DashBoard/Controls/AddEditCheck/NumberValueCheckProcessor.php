<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class NumberValueCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\NumberValueCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->operator = $data['operator'];
		$check->threshold = $data['threshold'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\NumberValueCheck();
	}


	/**
	 * @param \Pd\Monitoring\Check\NumberValueCheck $check
	 */
	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$url = \Pd\Monitoring\DashBoard\Forms\Controls\UrlControlFactory::create();
		$url->setOption('description', 'URL musí vracet HTTP stavový kód 200 a obsahovat pouze jedno číslo, např. 10 nebo 17.51');
		$form->addComponent($url, 'url');

		$form
			->addSelect('operator', 'Očekávaná hodnota', \Pd\Monitoring\Check\NumberValueCheck::OPERATORS)
			->setRequired(TRUE)
		;
		$thresholdHelp = 'Celé nebo desetinné číslo, např. 10 nebo 17.51';
		$form
			->addText('threshold', 'Mezní hodnota')
			->addRule(\Nette\Forms\Form::FLOAT, $thresholdHelp)
			->setRequired(TRUE)
			->setOption('description', $thresholdHelp)
		;
	}
}
