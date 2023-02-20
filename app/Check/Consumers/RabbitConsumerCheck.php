<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class RabbitConsumerCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\RabbitConsumerCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->lastConsumerCount = NULL;

		try {
			$config = [
				'verify' => $check->validateHttps,
			];
			$client = new \GuzzleHttp\Client($config);

			$options = [
				'connect_timeout' => 5,
				'timeout' => 5,
			];

			if ( ! empty($check->login)) {
				$options['auth'] = [$check->login, $check->password];
			}

			$this->logInfo($check, \sprintf('Kontrola ID "%s". Začínám stahovat url "%s".', $check->id, $check->url));

			$response = $client->request('GET', $check->url, $options);

			$this->logHeaders($check, $response);

			if ($response->getStatusCode() !== 200) {
				throw new \Pd\Monitoring\Exception();
			}

			$queues = \Nette\Utils\Json::decode((string) $response->getBody());

			$checkQueues = $check->getQueues();
			$consumers = [];
			foreach ($queues as $queue) {
				$key = \array_search($queue->name, $checkQueues, TRUE);
				if ($key !== FALSE) {
					$consumers[$key] = $queue->consumers;
				}
			}

			if (\count($checkQueues) > \count($consumers)) {
				foreach ($checkQueues as $k => $v) {
					if ( ! \array_key_exists($k, $consumers)) {
						$consumers[$k] = -1;
					}
				}
			}
			\ksort($consumers);
			$check->lastConsumerCount = \join(',', $consumers);

			return $check->status === \Pd\Monitoring\Check\ICheck::STATUS_OK;
		} catch (\Exception $e) {
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_CONSUMERS;
	}


	protected function getMaxAttempts(): int
	{
		return 10;
	}

}
