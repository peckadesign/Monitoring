<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class CertificateCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->daysBeforeWarning = $data['daysBeforeWarning'];
		$check->grade = $data['grade'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\CertificateCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$url = \Pd\Monitoring\DashBoard\Forms\Controls\DomainControlFactory::create();
		$form->addComponent($url, 'url');

		$form
			->addText('daysBeforeWarning', 'Počet dní předem')
			->setRequired(TRUE)
			->setType('number')
			->addRule(\Nette\Forms\Form::INTEGER)
		;

		$form
			->addSelect('grade', 'Očekávaná známka na SSL Labs', \array_combine(\Pd\Monitoring\Check\CertificateCheck::GRADES, \Pd\Monitoring\Check\CertificateCheck::GRADES))
			->setPrompt('Vyberte známku')
		;
	}

}
