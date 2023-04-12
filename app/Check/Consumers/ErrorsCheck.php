<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class ErrorsCheck extends Check
{

	private const TIMEOUT = 20;


	/**
	 * @param \Pd\Monitoring\Check\ErrorsCheck $check
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->errorsJson = NULL;

		$guzzleOptions = \Pd\Monitoring\Check\Consumers\Client\Configuration::create(self::TIMEOUT, 2 * self::TIMEOUT);
		$client = new \GuzzleHttp\Client($guzzleOptions->config());

		try {
			$start = (float) \microtime(TRUE);

			$this->logInfo($check, \sprintf('Začínám stahovat url "%s" v čase %f', $check->url, $start));

			$response = $client->get($check->url);

			$this->logHeaders($check, $response);

			$duration = (\microtime(TRUE) - $start) * 1000;

			$this->logInfo($check, \sprintf('Staženo za %f ms, návratový kód %u', $duration, $response->getStatusCode()));

			if ($response->getStatusCode() === 200) {
				$errorsJson = (string) $response->getBody();

				if ( ! \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ErrorsCheckProcessor::validateJsonList($errorsJson)) {
					return FALSE;
				}

				$size = \strlen($errorsJson);
				if ($size > 65500) {
					$this->logError($check, \sprintf('Json je příliš velký: %s bytes', $size));

					return FALSE;
				}

				$check->errorsJson = $errorsJson;

				return TRUE;
			}
		} catch (\GuzzleHttp\Exception\RequestException|\GuzzleHttp\Exception\ConnectException $e) {
			$this->logError($check, $e->getMessage());

			return FALSE;
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_ERRORS;
	}

}
