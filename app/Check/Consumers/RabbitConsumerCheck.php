<?php

namespace Pd\Monitoring\Check\Consumers;

class RabbitConsumerCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\RabbitConsumerCheck $check
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->lastConsumerCount = NULL;

		try {
			$client = new \GuzzleHttp\Client();

			$options = [
				'connect_timeout' => 5,
				'timeout' => 5,
			];

			if ( ! empty($check->login)) {
				$options['auth'] = [$check->login, $check->password];
			}

			$response = $client->request('GET', $check->url, $options);

			if ($response->getStatusCode() !== 200) {
				throw new \CI\Exception();
			}

			$queues = \Nette\Utils\Json::decode($response->getBody());

			$checkQueues = $check->getQueues();
			$consumers = [];
			foreach ($queues as $queue) {
				if (($key = array_search($queue->name, $checkQueues)) !== FALSE) {
					$consumers[$key] = $queue->consumers;
				}
			}

			if (count($checkQueues) > count($consumers)) {
				foreach ($checkQueues as $k => $v) {
					if ( ! array_key_exists($k, $consumers)) {
						$consumers[$k] = -1;
					}
				}
			}
			ksort($consumers);
			$check->lastConsumerCount = join(',', $consumers);

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
