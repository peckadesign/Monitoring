<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class RabbitQueueCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\RabbitQueueCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->lastMessageCount = NULL;

		$guzzleOptions = \Pd\Monitoring\Check\Consumers\Client\Configuration::create(5, 5, $check->validateHttps);
		$client = new \GuzzleHttp\Client($guzzleOptions->config());

		try {
			$options = [];
			if ( ! empty($check->login)) {
				$options['auth'] = [$check->login, $check->password];
			}

			$this->logInfo($check, \sprintf('Kontrola ID "%s". Začínám stahovat url "%s".', $check->id, $check->url));

			$response = $client->get($check->url, $options);

			$this->logHeaders($check, $response);

			if ($response->getStatusCode() !== 200) {
				throw new \Pd\Monitoring\Exception();
			}

			$queues = \Nette\Utils\Json::decode((string) $response->getBody());

			$checkQueues = $check->getQueues();
			$messages = [];
			foreach ($queues as $queue) {
				$key = \array_search($queue->name, $checkQueues, TRUE);
				if ($key !== FALSE) {
					$messages[$key] = $queue->messages;
				}
			}

			if (\count($checkQueues) > \count($messages)) {
				foreach ($checkQueues as $k => $v) {
					if ( ! \array_key_exists($k, $messages)) {
						$messages[$k] = -1;
					}
				}
			}
			\ksort($messages);
			$check->lastMessageCount = \join(',', $messages);

			return $check->status === \Pd\Monitoring\Check\ICheck::STATUS_OK;
		} catch (\Throwable $e) {
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_QUEUES;
	}


	protected function getMaxAttempts(): int
	{
		return 3;
	}

}
