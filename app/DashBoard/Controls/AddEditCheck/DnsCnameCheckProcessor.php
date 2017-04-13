<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class DnsCnameCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data)
	{
		$check->url = $data['url'];
		$check->target = $data['target'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\DnsCnameCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form)
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'Adresa')
			->setRequired(TRUE)
		;
		$form
			->addText('target', 'Cíl')
			->setRequired(TRUE)
			->addFilter(function($value) {
				return rtrim($value, '.');
			});
		;
	}

}
