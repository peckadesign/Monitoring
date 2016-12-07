<?php

namespace Pd\Monitoring\Check\Consumers;

class FeedCheck implements \Kdyby\RabbitMq\IConsumer
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

		if ( ! $check || ! $check instanceof \Pd\Monitoring\Check\FeedCheck) {
			return self::MSG_REJECT;
		}

		$check->lastCheck = new \DateTime();

		$ch = curl_init($check->url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);    // we want headers
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);    // we don't need body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$output = curl_exec($ch);

		$check->lastModified = NULL;
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
			$check->lastSize = NULL;
		} else {
			$check->lastSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

			foreach (explode("\n", $output) as $line) {
				if (stripos($line, 'last-modified:') !== FALSE) {
					$check->lastModified = new \DateTime(trim(str_ireplace('last-modified:', '', $line)));
					break;
				}
			}
		}
		curl_close($ch);

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}
}