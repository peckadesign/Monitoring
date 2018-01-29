<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class RabbitQueueCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->url = $data['url'];
		if ($check->getPersistedId() && $check->queues !== $data['queues']) {
			$check->lastMessageCount = NULL;
		}
		$check->queues = $data['queues'];
		$check->maximumMessageCount = $data['maximumMessageCount'];
		$check->login = $data['login'];
		if ( ! empty($data['password'])) {
			$check->password = $data['password'];
		}
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\RabbitQueueCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('queues', 'Fronty, oddělené čárkou')
			->setRequired(TRUE)
		;
		$form
			->addText('maximumMessageCount', 'Maximální počty zpráv, oddělené čárkou')
			->setRequired(TRUE)
		;
		$form
			->addText('url', 'URL API nebo skriptu')
			->setRequired(TRUE)
		;
		$form
			->addText('login', 'login k API')
		;
		$form
			->addPassword('password', 'Heslo k API')
		;

	}

}
