<?php

namespace Pd\Monitoring\Check\Consumers;

class DnsCheck implements \Kdyby\RabbitMq\IConsumer
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

		if ( ! $check || ! $check instanceof \Pd\Monitoring\Check\DnsCheck) {
			return self::MSG_REJECT;
		}

		$check->lastCheck = new \DateTime();
		$check->lastIp = gethostbyname($check->url);

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}
}
