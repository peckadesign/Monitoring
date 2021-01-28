<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class RabbitConsumerCheckProcessor implements ICheckControlProcessor
{

	/**
	 * @param \Pd\Monitoring\Check\RabbitConsumerCheck $check
	 */
	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->adminUrl = $data['adminUrl'];
		if ($check->getPersistedId() && $check->queues !== $data['queues']) {
			$check->lastConsumerCount = NULL;
		}
		$check->queues = $data['queues'];
		$check->minimumConsumerCount = $data['minimumConsumerCount'];
		$check->login = $data['login'];
		if ( ! empty($data['password'])) {
			$check->password = $data['password'];
		}
		$check->validateHttps = $data['validateHttps'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\RabbitConsumerCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$url = \Pd\Monitoring\DashBoard\Forms\Controls\UrlControlFactory::create();
		$url->setOption('description', 'URL musí vracet stejný výsledek jako volání "/api/queues" pluginu RabbitMQ Management HTTP API.');
		$form->addComponent($url, 'url');

		$form
			->addText('queues', 'Fronty')
			->setRequired(TRUE)
			->setOption('description', 'Více front oddělte čárkou.')
		;
		$form
			->addText('minimumConsumerCount', 'Minimální počty')
			->setRequired(TRUE)
			->setOption('description', 'Hodnoty oddělte čárkou, uvádějte ve stejném pořadí, jako fronty.')
		;
		$form
			->addText('adminUrl', 'Administrační URL')
			->setRequired(FALSE)
			->setOption('description', 'Odkaz na webovou administraci RabbitMQ')
		;
		$form
			->addText('login', 'HTTP login k URL');
		$form
			->addPassword('password', 'HTTP heslo k URL');
		$form
			->addCheckbox('validateHttps', 'Validovat HTTPS certifikát')
			->setDefaultValue(TRUE)
		;

		/**
		 * @param array<mixed> $values
		 */
		$form->onValidate[] = static function (\Nette\Application\UI\Form $form, array $values): void
		{
			$queues = \count(\explode(',', $values['queues']));
			$minimumConsumerCount = \count(\explode(',', $values['minimumConsumerCount']));
			if ($queues !== $minimumConsumerCount) {
				$form->addError('Pro každou frontu musí být zadán minimální počet consumerů');
				$form['queues']->addError('Počet prvků: ' . $queues);
				$form['minimumConsumerCount']->addError('Počet prvků: ' . $minimumConsumerCount);
			}
		};
	}

}
