<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class NumberValueCheck extends Check
{

	private const TIMEOUT = 5;

	/**
	 * @param \Pd\Monitoring\Check\NumberValueCheck $check
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$client = new \GuzzleHttp\Client();

		$check->value = NULL;

		$options = [
			'connect_timeout' => self::TIMEOUT,
			'timeout' => 2 * self::TIMEOUT,
			'headers' => [
				'User-Agent' => 'PeckaMonitoringBot/1.0',
			],
		];

		try {
			$start = (float) \microtime(TRUE);

			$this->logInfo($check, \sprintf('Začínám stahovat url "%s" v čase %f', $check->url, $start));

			$response = $client->request('GET', $check->url, $options);

			$this->logHeaders($check, $response);

			$duration = (\microtime(TRUE) - $start) * 1000;

			$this->logInfo($check, \sprintf('Staženo za %f ms, návratový kód %u', $duration, $response->getStatusCode()));

			if ($response->getStatusCode() === 200) {
				$responseValue = (string) $response->getBody();
				if ( ! \Nette\Utils\Validators::isNumeric($responseValue)) {
					return FALSE;
				}
				$check->value = (float) $responseValue;

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
		return \Pd\Monitoring\Check\ICheck::TYPE_NUMBER_VALUE;
	}
}
