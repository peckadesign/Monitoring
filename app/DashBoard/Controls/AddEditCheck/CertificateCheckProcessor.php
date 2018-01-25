<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class CertificateCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data)
	{
		$check->url = $data['url'];
		$check->daysBeforeWarning = $data['daysBeforeWarning'];
		$check->grade = $data['grade'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\CertificateCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form)
	{
		$form->addGroup($check->getTitle());

		$form['url'] = (new \Pd\Monitoring\DashBoard\Forms\Controls\DomainControl('Doména'))
			->setRequired(TRUE)
		;

		$form
			->addText('daysBeforeWarning', 'Počet dní předem')
			->setRequired(TRUE)
			->setType('number')
			->addRule(\Nette\Forms\Form::INTEGER)
		;

		$form
			->addSelect('grade', 'Očekávaná známka na SSL Labs', array_combine(\Pd\Monitoring\Check\CertificateCheck::GRADES, \Pd\Monitoring\Check\CertificateCheck::GRADES))
			->setPrompt('Vyberte známku')
		;
	}

}
