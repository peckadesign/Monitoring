<?php

namespace Pd\Monitoring\DashBoard\Controls\AddCheck;

class AliveCheckControl extends CheckControl
{

	protected function processNewEntity(array $data)
	{
		$this->check->url = $data['url'];
		$this->check->timeout = $data['timeout'];
	}


	protected function getCheck() : \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\AliveCheck();
	}


	protected function createAddForm(\Nette\Application\UI\Form $form)
	{
		$form->addGroup($this->check->getTitle());
		$form
			->addText('url', 'URL')
			->setRequired(TRUE)
		;
		$form
			->addText('timeout', 'Timeout')
			->setRequired(TRUE)
			->setOption('input-append', 's')
		;
	}
}
