<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

final class XpathCheck extends Check
{

	/**
	 * @var \Pd\Monitoring\Check\SiteMapLoader|null
	 */
	private $siteMapLoader;

	/**
	 * @var string|null
	 */
	private $lastUrl;


	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\XpathCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->xpathLastResult = NULL;

		$client = new \GuzzleHttp\Client();

		$options = [
			'connect_timeout' => $check::ALIVE_TIMEOUT / 1000,
			'timeout' => 2 * $check::ALIVE_TIMEOUT / 1000,
			'headers' => [
				'User-Agent' => 'PeckaMonitoringBot/1.0',
			],
			'allow_redirects' => TRUE,
		];

		if ($check->siteMap && ! $this->siteMapLoader) {
			$this->siteMapLoader = new \Pd\Monitoring\Check\SiteMapLoader($check->url);
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
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_XPATH;
	}


	private function loadUrl(\GuzzleHttp\Client $client, array $options, \Pd\Monitoring\Check\XpathCheck $check, string $url): bool
	{
		$this->logInfo($check, \sprintf('Začínám stahovat url "%s"', $url));

		$response = $client->request('GET', $url, $options);

		$this->logHeaders($check, $response);

		if ($response->getStatusCode() !== 200) {
			return FALSE;
		}

		$body = (string) $response->getBody();

		if ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_MATCH) {
			$m = \Atrox\Matcher::single($check->xpath)->fromHtml();
		} else {
			$m = \Atrox\Matcher::count($check->xpath)->fromHtml();
		}

		$extractedData = $m($body);

		$check->xpathLastResult = $extractedData;

		if ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_MATCH) {
			return $extractedData == $check->xpathResult;
		} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
			return $extractedData < $check->xpathResult;
		} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_EQ) {
			return $extractedData == $check->xpathResult;
		} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_GT) {
			return $extractedData > $check->xpathResult;
		}

		return FALSE;
	}

}
