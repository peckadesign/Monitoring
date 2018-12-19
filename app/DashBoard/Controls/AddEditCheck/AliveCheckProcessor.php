<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class AliveCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->url = $data['url'];
		$check->followRedirect = $data['followRedirect'];
	}


	public function getCheck() : \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\AliveCheck();
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
			->addCheckbox('followRedirect', 'Následovat přesměrování')
			->setOption('description', 'Poslední URL musí vracet HTTP stavový kód 200.')
		;
	}

}
