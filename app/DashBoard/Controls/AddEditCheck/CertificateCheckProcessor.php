<?php

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class CertificateCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data)
	{
		$check->url = $data['url'];
		$check->daysBeforeWarning = $data['daysBeforeWarning'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\CertificateCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form)
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'Adresa')
			->setRequired(TRUE)
		;
		$form
			->addText('daysBeforeWarning', 'Počet dní předem')
			->setRequired(TRUE)
		;
	}

}
