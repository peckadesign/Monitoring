<?php

namespace Pd\Monitoring\DashBoard\Controls\AddCheck;

class TermCheckControl extends CheckControl
{

	protected function processNewEntity(array $data)
	{
		$this->check->message = $data['message'];
		$this->check->term = $data['term'];
	}


	protected function getCheck() : \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\TermCheck();
	}


	protected function createAddForm(\Nette\Application\UI\Form $form)
	{
		$form->addGroup($this->check->getTitle());
		$form
			->addText('message', 'Upomínka')
			->setRequired(TRUE)
		;
		$form
			->addDateTimePicker('term', 'Datum a čas')
			->setRequired(TRUE)
		;
	}
}
