<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

final class XpathCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\XpathCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->url = $data['url'];
		$check->xpath = $data['xpath'];
		$check->operator = $data['operator'];
		$check->xpathResult = $data['xpathResult'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\XpathCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'URL')
			->setRequired(TRUE)
			->setAttribute('placeholder', 'https://www.example.com')
			->setOption('description', 'URL musí vracet HTTP stavový kód 200.')
		;

		$form
			->addText('xpath', 'XPath selektor')
			->setOption('description', 'Např. "//*[@id="main"]/ul/li"')
		;
		$form->addSelect('operator', 'Výsledek nálezu', \Pd\Monitoring\Check\XpathCheck::OPERATORS);
		$form->addText('xpathResult', 'Očekávaný výsledek');
	}

}
