<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

final class XpathCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\XpathCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->xpath = $data['xpath'];
		$check->operator = $data['operator'];
		$check->xpathResult = $data['xpathResult'];
		$check->siteMap = $data['siteMap'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\XpathCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$url = \Pd\Monitoring\DashBoard\Forms\Controls\UrlControlFactory::create();
		$form->addComponent($url, 'url');

		$form
			->addCheckbox('siteMap', 'URL obsahuje sitemapu')
			->setOption('description', 'Kontrola se provede na všechny URL v sitemapě')
		;

		$form
			->addText('xpath', 'XPath selektor')
			->setOption('description', 'Např. "//*[@id="main"]/ul/li"')
		;
		$form->addSelect('operator', 'Výsledek nálezu', \Pd\Monitoring\Check\XpathCheck::OPERATORS);
		$form->addText('xpathResult', 'Očekávaný výsledek');
	}

}
