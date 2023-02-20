<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class HttpStatusCodeCheck extends Check
{

	private const TIMEOUT = 30;


	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\HttpStatusCodeCheck $check
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		try {
			$config = [
				'verify' => FALSE,
				'allow_redirects' => FALSE,
			];

			$options = [
				'connect_timeout' => self::TIMEOUT,
				'timeout' => 2 * self::TIMEOUT,
				'headers' => [
					'User-Agent' => 'PeckaMonitoringBot/1.0',
				],
			];

			$client = new \GuzzleHttp\Client($config);
			try {
				$this->logInfo($check, \sprintf('Kontrola ID "%s". Začínám stahovat url "%s".', $check->id, $check->url));

				$response = $client->get($check->url, $options);

				$this->logHeaders($check, $response);

				$check->lastCode = $response->getStatusCode();
			} catch (\GuzzleHttp\Exception\BadResponseException $e) {
				$check->lastCode = (int) $e->getCode();
			}

			return TRUE;
		} catch (\Throwable $e) {
			$this->logError($check, $e->getMessage());
		}

		return FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_HTTP_STATUS_CODE;
	}

}
