<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class AliveCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->followRedirect = $data['followRedirect'];
		$check->siteMap = $data['siteMap'];
	}


	public function getCheck() : \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\AliveCheck();
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
			->addCheckbox('followRedirect', 'Následovat přesměrování')
			->setOption('description', 'Poslední URL musí vracet HTTP stavový kód 200.')
		;
	}

}
