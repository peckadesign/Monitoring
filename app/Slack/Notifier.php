<?php declare(strict_types = 1);

namespace Pd\Monitoring\Slack;

class Notifier
{

	private \Monolog\Logger $logger;

	private \Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider;


	public function __construct(
		\Monolog\Logger $logger,
		\Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider
	)
	{
		$this->logger = $logger;
		$this->dateTimeProvider = $dateTimeProvider;
	}


	/**
	 * @param array|Button[] $buttons
	 */
	public function notify(string $hookUrl, string $channel, string $message, string $color, array $buttons): void
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

		foreach ($buttons as $button) {
			$payload['attachments'][0]['actions'][] = ['type' => 'button', 'name' => $button->getName(), 'text' => $button->getText(), 'url' => $button->getUrl()];
		}

		$options = [
			'json' => $payload,
		];

		try {
			$client = new \GuzzleHttp\Client();
			$client->request('POST', $hookUrl, $options);
		} catch (\Throwable $e) {
			$this->logger->error($e);
		}
	}

}
