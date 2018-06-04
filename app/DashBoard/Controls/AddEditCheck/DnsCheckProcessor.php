<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class DnsCheckProcessor implements ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\DnsCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->url = $data['url'];
		$check->dnsValue = $data['dnsValue'];
		$check->dnsType = $data['dnsType'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\DnsCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$form->addGroup($check->getTitle());

		$form['url'] = (new \Pd\Monitoring\DashBoard\Forms\Controls\DomainControl('Doména'))
			->setRequired(TRUE)
		;

		$form
			->addSelect('dnsType', 'Typ DNS záznamu', \array_combine(\Pd\Monitoring\Check\DnsCheck::$dnsTypes, \Pd\Monitoring\Check\DnsCheck::$dnsTypes))
			->setRequired(TRUE)
		;

		$form
			->addText('dnsValue', 'Hodnota DNS záznamu')
			->setRequired(TRUE)
		;
	}

}
