<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

final class XpathCheck extends Check
{

	private ?\Pd\Monitoring\Check\SiteMapLoader $siteMapLoader = NULL;

	private ?string $lastUrl = NULL;


	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		if ( ! $check instanceof \Pd\Monitoring\Check\XpathCheck) {
			throw new \Pd\Monitoring\Exception();
		}
		$check->xpathLastResult = NULL;

		$guzzleOptions = \Pd\Monitoring\Check\Consumers\Client\Configuration::create($check::ALIVE_TIMEOUT / 1000, (int) (\round(2 * ($check::ALIVE_TIMEOUT / 1000))))
			->withAllowRedirects(\Pd\Monitoring\Check\Consumers\Client\Configuration\AllowRedirects::create(TRUE))
		;
		$client = new \GuzzleHttp\Client($guzzleOptions->config());

		if ($check->siteMap && ! $this->siteMapLoader) {
			$this->siteMapLoader = new \Pd\Monitoring\Check\SiteMapLoader($check->url);
		}

		try {
			if ($this->siteMapLoader) {
				$withoutError = TRUE;
				while ($url = $this->siteMapLoader->getNextUrl($this->lastUrl)) {
					$loaded = $this->loadUrl($client, $check, $url);
					if ( ! $loaded) {
						$this->logError($check, \sprintf('Došlo k chybě na url "%s"', $url));
						$withoutError = FALSE;
					}
					$this->lastUrl = $url;
				}

				return $withoutError;
			} else {
				$loaded = $this->loadUrl($client, $check, $check->url);
				if ($loaded) {
					return TRUE;
				}
				$this->logError($check, \sprintf('Došlo k chybě na url "%s"', $check->url));
			}
		} catch (\GuzzleHttp\Exception\RequestException|\GuzzleHttp\Exception\ConnectException $e) {
			$this->logError($check, $e->getMessage());
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_XPATH;
	}


	private function loadUrl(\GuzzleHttp\Client $client, \Pd\Monitoring\Check\XpathCheck $check, string $url): bool
	{
		$response = $client->get($url);

		if ($response->getStatusCode() !== 200) {
			$this->logHeaders($check, $response);

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
			return $extractedData === $check->xpathResult;
		} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
			return $extractedData < $check->xpathResult;
		} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_EQ) {
			return $extractedData === $check->xpathResult;
		} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_GT) {
			return $extractedData > $check->xpathResult;
		}

		return FALSE;
	}


	protected function getMaxAttempts(): int
	{
		return 1;
	}

}
