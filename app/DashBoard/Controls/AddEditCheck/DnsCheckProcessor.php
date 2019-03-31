<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class DnsCheckProcessor implements ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\DnsCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->dnsValue = $data['dnsValue'];
		$check->dnsType = $data['dnsType'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\DnsCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$url = \Pd\Monitoring\DashBoard\Forms\Controls\DomainControlFactory::create();
		$form->addComponent($url, 'url');

		$form
			->addSelect('dnsType', 'Typ DNS záznamu', \array_combine(\Pd\Monitoring\Check\DnsCheck::$dnsTypes, \Pd\Monitoring\Check\DnsCheck::$dnsTypes))
			->setRequired(TRUE)
		;

		$form
			->addText('dnsValue', 'Hodnota DNS záznamu')
			->setRequired(TRUE)
			->setOption('description', 'Více hodnot oddělte středníkem, na pořadí nezáleží.')
		;
	}

}
