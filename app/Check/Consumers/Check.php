<?php declare(strict_types=1);

namespace Pd\Monitoring\Check\Consumers;

abstract class Check implements \Kdyby\RabbitMq\IConsumer
{

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;

	/**
	 * @var \Pd\Monitoring\Orm\Orm
	 */
	private $orm;

	/**
	 * @var \Pd\Monitoring\Check\IOnCheckChangeDispatcher
	 */
	private $onCheckChangeDispatcher;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider,
		\Pd\Monitoring\Orm\Orm $orm,
		\Pd\Monitoring\Check\IOnCheckChangeDispatcher $onCheckChangeDispatcher
	) {
		$this->checksRepository = $checksRepository;
		$this->dateTimeProvider = $dateTimeProvider;
		$this->orm = $orm;
		$this->onCheckChangeDispatcher = $onCheckChangeDispatcher;
	}


	public function process(\PhpAmqpLib\Message\AMQPMessage $message): int
	{
		$checkId = (int) $message->getBody();

		$this->orm->clearIdentityMapAndCaches(\Nextras\Orm\Model\IModel::I_KNOW_WHAT_I_AM_DOING);

		/** @var \Pd\Monitoring\Check\AliveCheck $check */
		$check = $this->checksRepository->getById($checkId);

		if ( ! $check || $check->type !== $this->getCheckType()) {
			return self::MSG_REJECT;
		}

		$maxAttempts = $this->getMaxAttempts();
		$attempts = 0;

		$oldStatus = $check->status;

		do {
			$check->lastCheck = $this->dateTimeProvider->getDateTime();

			$result = $this->doHardJob($check);
		} while ( ! $result && ++$attempts < $maxAttempts && sleep(3) === 0);

		if ($oldStatus !== $check->status) {
			$this->onCheckChangeDispatcher->change($check);
		}

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}


	/**
	 * @param \Pd\Monitoring\Check\Check $check
	 * @return bool TRUE, pokud se podařilo úspěšně provést kontrolu, jinak FALSE. Po FALSE může následovat opětovné spuštění kontroly
	 */
	abstract protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool;


	abstract protected function getCheckType(): int;


	protected function getMaxAttempts(): int
	{
		return 2;
	}

}
