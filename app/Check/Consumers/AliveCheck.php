<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class AliveCheck extends Check
{

	private ?\Pd\Monitoring\Check\SiteMapLoader $siteMapLoader = NULL;

	private ?string $lastUrl = NULL;


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

		if ($check->siteMap && ! $this->siteMapLoader) {
			$this->siteMapLoader = new \Pd\Monitoring\Check\SiteMapLoader($check->url);
		}

		if ( ! $check->followRedirect) {
			$options['allow_redirects'] = FALSE;
		}

		try {
			if ($this->siteMapLoader) {
				while ($url = $this->siteMapLoader->getNextUrl($this->lastUrl)) {
					$loaded = $this->loadUrl($client, $options, $check, $url);
					if ( ! $loaded) {
						return FALSE;
					} else {
						$this->lastUrl = $url;
					}
				}

				return TRUE;
			} else {
				$loaded = $this->loadUrl($client, $options, $check, $check->url);
				if ($loaded) {
					return TRUE;
				}
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


	private function loadUrl(\GuzzleHttp\Client $client, array $options, \Pd\Monitoring\Check\AliveCheck $check, string $url): bool
	{
		$start = (float) \microtime(TRUE);
		$this->logInfo($check, \sprintf('Začínám stahovat url "%s"', $url));

		$response = $client->request('GET', $url, $options);
		$duration = (\microtime(TRUE) - $start) * 1000;

		$this->logHeaders($check, $response);

		$this->logInfo($check, \sprintf('Staženo za %.2f ms, návratový kód %u', $duration, $response->getStatusCode()));

		if ($response->getStatusCode() === 200) {
			$check->lastTimeout = $duration;

			return TRUE;
		}

		return FALSE;
	}

}
