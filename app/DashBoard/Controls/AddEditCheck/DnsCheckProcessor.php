<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

use Pd\Monitoring\Check\DnsCheck;

class DnsCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data)
	{
		$check->url = $data['url'];
		$check->ip = $data['ip'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\DnsCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form)
	{
		$form->addGroup($check->getTitle());

		$form['url'] = (new \Pd\Monitoring\DashBoard\Forms\Controls\DomainControl('Doména'))
			->setRequired(TRUE)
		;

		$form
			->addText('ip', 'IP adresa')
			->setRequired(TRUE)
			->addFilter(function($value) {
				return DnsCheck::normalizeIpList($value);
			})
		;
	}

}
