<?php

namespace Pd\Monitoring\Check\Consumers;

class RabbitConsumerCheck implements \Kdyby\RabbitMq\IConsumer
{

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		$this->checksRepository = $checksRepository;
	}


	public function process(\PhpAmqpLib\Message\AMQPMessage $message): int
	{
		$checkId = $message->getBody();

		/** @var \Pd\Monitoring\Check\DnsCheck $check */
		$check = $this->checksRepository->getById($checkId);

		if ( ! $check || ! $check instanceof \Pd\Monitoring\Check\RabbitConsumerCheck) {
			return self::MSG_REJECT;
		}

		$check->lastCheck = new \DateTime();
		$check->lastConsumerCount = NULL;

		try {
			$client = new \GuzzleHttp\Client();

			$options = [
				'connect_timeout' => 5,
				'timeout' => 5,
			];

			if ( ! empty($check->login)) {
				$options['auth'] = [$check->login, $check->password];
			}

			$response = $client->request('GET', $check->url, $options);

			if ($response->getStatusCode() !== 200) {
				throw new \CI\Exception();
			}

			$queues = \Nette\Utils\Json::decode($response->getBody());

			$checkQueues = $check->getQueues();
			$consumers = [];
			foreach ($queues as $queue) {
				if (($key = array_search($queue->name, $checkQueues)) !== FALSE) {
					$consumers[$key] = $queue->consumers;
				}
			}

			if (count($checkQueues) > count($consumers)) {
				foreach ($checkQueues as $k => $v) {
					if ( ! array_key_exists($k, $consumers)) {
						$consumers[$k] = -1;
					}
				}
			}
			ksort($consumers);
			$check->lastConsumerCount = join(',', $consumers);
		} catch (\Exception $e) {
		}

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}
}
