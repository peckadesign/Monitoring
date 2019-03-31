<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class HttpStatusCodeCheck extends Check
{

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
			$client = new \GuzzleHttp\Client($config);
			try {
				$response = $client->request('GET', $check->url);

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
