<?php

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class HddSpaceCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data)
	{
		$check->url = $data['url'];
		$check->percent = $data['percent'];
	}


	public function getCheck() : \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\HddSpaceCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form)
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'URL se skriptem')
			->setRequired(TRUE)
		;
		$form
			->addText('percent', 'Minimální volné místo v %')
			->setRequired(TRUE)
		;
	}
}
