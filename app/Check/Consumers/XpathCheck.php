<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

final class XpathCheck extends Check
{

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

		try {
			$response = $client->request('GET', $check->url, $options);

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
			} elseif ($check->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
				return $extractedData > $check->xpathResult;
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
}
