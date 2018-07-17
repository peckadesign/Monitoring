<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class NumberValueCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\NumberValueCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->url = $data['url'];
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
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'URL')
			->setRequired(TRUE)
			->setAttribute('placeholder', 'https://www.example.com/api/value')
			->setOption('description', 'URL musí vracet HTTP stavový kód 200 a obsahovat pouze jedno číslo')
		;
		$form
			->addSelect('operator', 'Očekávaná hodnota', \Pd\Monitoring\Check\NumberValueCheck::OPERATORS)
			->setRequired(TRUE)
		;
		$form
			->addText('threshold', 'Mezní hodnota')
			->setRequired(TRUE)
		;
	}
}
