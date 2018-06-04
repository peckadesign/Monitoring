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

		try {
			$config = [
				'verify' => FALSE,
			];
			$client = new \GuzzleHttp\Client($config);

			$options = [
				'connect_timeout' => 5,
				'timeout' => 5,
			];

			if ( ! empty($check->login)) {
				$options['auth'] = [$check->login, $check->password];
			}

			$response = $client->request('GET', $check->url, $options);

			if ($response->getStatusCode() !== 200) {
				throw new \Pd\Monitoring\Exception();
			}

			$queues = \Nette\Utils\Json::decode($response->getBody());

			$checkQueues = $check->getQueues();
			$messages = [];
			foreach ($queues as $queue) {
				if (($key = \array_search($queue->name, $checkQueues, TRUE)) !== FALSE) {
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
