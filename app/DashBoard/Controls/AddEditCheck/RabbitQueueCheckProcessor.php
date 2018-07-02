<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class RabbitQueueCheckProcessor implements ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\RabbitQueueCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->url = $data['url'];
		$check->adminUrl = $data['adminUrl'];
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
			->addText('queues', 'Fronty')
			->setRequired(TRUE)
			->setOption('description', 'Více front oddělte čárkou.')
		;
		$form
			->addText('maximumMessageCount', 'Maximální počty zpráv')
			->setRequired(TRUE)
			->setOption('description', 'Hodnoty oddělte čárkou, uvádějte ve stejném pořadí, jako fronty.')
		;
		$form
			->addText('url', 'URL')
			->setRequired(TRUE)
			->setOption('description', 'URL musí vracet stejný výsledek jako volání "/api/queues" pluginu RabbitMQ Management HTTP API.')
		;
		$form
			->addText('adminUrl', 'Administrační URL')
			->setRequired(FALSE)
			->setOption('description', 'Odkaz na webovou administraci RabbitMQ')
		;
		$form
			->addText('login', 'HTTP login k URL')
		;
		$form
			->addPassword('password', 'HTTP heslo k URL')
		;

	}

}
