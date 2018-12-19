<?php declare(strict_types = 1);

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
			'headers' => [
				'User-Agent' => 'PeckaMonitoringBot/1.0',
			],
		];

		if ( ! $check->followRedirect) {
			$options['allow_redirects'] = FALSE;
		}

		try {
			$start = (float) \microtime(TRUE);

			$this->logInfo($check, \sprintf('Začínám stahovat url "%s" v čase %f', $check->url, $start));

			$response = $client->request('GET', $check->url, $options);
			$duration = (\microtime(TRUE) - $start) * 1000;

			$this->logInfo($check, \sprintf('Staženo za %f ms, návratový kód %u', $duration, $response->getStatusCode()));

			if ($response->getStatusCode() === 200) {
				$check->lastTimeout = $duration;

				return TRUE;
			}
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			$this->logError($check, $e->getMessage());

			return FALSE;
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_ALIVE;
	}
}
