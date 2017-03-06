<?php

namespace Pd\Monitoring\Slack;

class Notifier
{

	/**
	 * @var string
	 */
	private $hookUrl;

	/**
	 * @var \Monolog\Logger
	 */
	private $logger;

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function __construct(
		string $hookUrl,
		\Monolog\Logger $logger,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider
	) {
		$this->hookUrl = $hookUrl;
		$this->logger = $logger;
		$this->dateTimeProvider = $dateTimeProvider;
	}


	public function notify(string $channel, string $message, string $color)
	{
		$payload = [
			'channel' => $channel,
			'username' => 'Monitoring',
			'icon_emoji' => ':eye:',
			'attachments' => [
				[
					'text' => $message,
					'color' => $color,
					'ts' => $this->dateTimeProvider->getDateTime()->format('U'),
				],
			],
		];

		$options = [
			'json' => $payload,
		];

		try {
			$client = new \GuzzleHttp\Client();
			$client->request('POST', $this->hookUrl, $options);
		} catch (\Throwable $e) {
			$this->logger->addError($e);
		}
	}
}
