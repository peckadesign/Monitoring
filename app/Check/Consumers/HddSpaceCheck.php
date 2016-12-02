<?php

namespace Pd\Monitoring\Check\Consumers;

class HddSpaceCheck implements \Kdyby\RabbitMq\IConsumer
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

		/** @var \Pd\Monitoring\Check\HddSpaceCheck $check */
		$check = $this->checksRepository->getById($checkId);

		if ( ! $check || ! $check instanceof \Pd\Monitoring\Check\HddSpaceCheck) {
			return self::MSG_REJECT;
		}

		$client = new \GuzzleHttp\Client();
		try {
			$response = $client->request('GET', $check->url);
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$response = NULL;
		}

		if ($response) {
			$json = $response->getBody()->getContents();
			$data = (array) json_decode($json);
		}

		$check->lastCheck = new \DateTime();

		if ($response === NULL || $response->getStatusCode() !== 200 || ! isset($data['free'], $data['total'])) {
			$check->freeSpace = NULL;
			$check->totalSpace = NULL;
		} else {
			$check->freeSpace = $data['free'] * 1.0;
			$check->totalSpace = $data['total'] * 1.0;
		}

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}
}
