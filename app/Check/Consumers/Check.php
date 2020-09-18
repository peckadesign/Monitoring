<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

abstract class Check implements \Kdyby\RabbitMq\IConsumer
{

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\Utils\IDateTimeProvider
	 */
	private $dateTimeProvider;

	/**
	 * @var \Monolog\Logger
	 */
	private $logger;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider,
		\Monolog\Logger $logger
	) {
		$this->checksRepository = $checksRepository;
		$this->dateTimeProvider = $dateTimeProvider;
		$this->logger = $logger;
	}


	public function process(\PhpAmqpLib\Message\AMQPMessage $message): int
	{
		$checkId = (int) $message->getBody();

		$this->checksRepository->doClear();

		/** @var \Pd\Monitoring\Check\AliveCheck $check */
		$check = $this->checksRepository->getById($checkId);

		if ( ! $check || $check->type !== $this->getCheckType()) {
			return self::MSG_REJECT;
		}

		$maxAttempts = $this->getMaxAttempts();
		$attempts = 0;

		do {
			$this->checksRepository->doClear();
			$check = $this->checksRepository->getById($checkId);

			$this->logInfo($check, \sprintf('Pokus číslo %u', $attempts));
			$check->lastCheck = $this->dateTimeProvider->getDateTime();

			$result = $this->doHardJob($check);

			if ($result && $check->status !== \Pd\Monitoring\Check\ICheck::STATUS_OK && ($attempts+1) < $maxAttempts) {
				$result = FALSE;
			}

		} while ( ! $result && ++$attempts < $maxAttempts && \sleep(3) === 0);

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


	protected function logInfo(\Pd\Monitoring\Check\Check $check, string $message): void
	{
		$this->logger->info($message, ['check' => $check->id, 'checkType' => $check->type]);
	}


	protected function logError(\Pd\Monitoring\Check\Check $check, string $message): void
	{
		$this->logger->error($message, ['check' => $check->id, 'checkType' => $check->type]);
	}


	protected function logHeaders(\Pd\Monitoring\Check\Check $check, \Psr\Http\Message\ResponseInterface $response): void
	{
		$headers = [];
		foreach ($response->getHeaders() as $headerName => $headerValues) {
			$headers[] = $headerName . ': ' . \implode(', ', $headerValues);
		}
		$this->logInfo($check, 'Hlavičky odpovědi:' . "\n" . 'HTTP/' . $response->getProtocolVersion() . ' ' . $response->getStatusCode() . ' ' . ($response->getReasonPhrase() ?: '') . "\n" . \implode(";\n", $headers));
	}

}
