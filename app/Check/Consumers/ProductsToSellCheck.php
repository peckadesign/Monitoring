<?php

namespace Pd\Monitoring\Check\Consumers;

class ProductsToSellCheck implements \Kdyby\RabbitMq\IConsumer
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

		/** @var \Pd\Monitoring\Check\AliveCheck $check */
		$check = $this->checksRepository->getById($checkId);

		if ( ! $check || ! $check instanceof \Pd\Monitoring\Check\ProductsToSellCheck) {
			return self::MSG_REJECT;
		}

		if ($check->lastCount !== NULL) {
			$check->previousCount = $check->lastCount;
		}

		$check->lastCount = file_get_contents($check->url) * 1;
		$check->lastCheck = new \DateTime();

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}
}
