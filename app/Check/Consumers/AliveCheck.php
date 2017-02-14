<?php

namespace Pd\Monitoring\Check\Consumers;

class AliveCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\AliveCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$client = new \GuzzleHttp\Client();

		$check->beforeLastTimeout = $check->lastTimeout;
		$check->lastTimeout = NULL;

		$options = [
			'connect_timeout' => $check::ALIVE_TIMEOUT / 1000,
			'timeout' => 2 * $check::ALIVE_TIMEOUT / 1000,
		];

		try {
			$start = microtime(TRUE);

			$response = $client->request('GET', $check->url, $options);
			$duration = (microtime(TRUE) - $start) * 1000;

			if ($response->getStatusCode() === 200) {
				$check->lastTimeout = $duration;

				return TRUE;
			}
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			return FALSE;
		}

		return FALSE;
	}
}
