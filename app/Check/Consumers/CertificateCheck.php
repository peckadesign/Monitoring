<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class CertificateCheck extends Check
{

	private const TIMEOUT = 20;


	private \Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider,
		\Monolog\Logger $logger
	)
	{
		parent::__construct($checksRepository, $dateTimeProvider, $logger);

		$this->dateTimeProvider = $dateTimeProvider;
	}


	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\CertificateCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		\set_error_handler(static function ($code, $message)
		{
			\restore_error_handler();
			throw new \Pd\Monitoring\Exception($message, $code);
		}, \E_ALL);

		try {
			try {
				$get = \stream_context_create(["ssl" => ["capture_peer_cert" => TRUE]]);
				$read = \stream_socket_client("ssl://" . $check->url . ":443", $errno, $errstr, 30, \STREAM_CLIENT_CONNECT, $get);
				\restore_error_handler();
				$cert = \stream_context_get_params($read);
				$certinfo = \openssl_x509_parse($cert['options']['ssl']['peer_certificate'], TRUE);

				if (empty($certinfo['validTo_time_t'])) {
					throw new \InvalidArgumentException('No certificate data');
				}

				$date = $this->dateTimeProvider->getDateTime();
				$date = $date->setTimestamp($certinfo['validTo_time_t']);

				$check->lastValiddate = $date;
			} catch (\Exception $e) {
				$check->lastValiddate = NULL;

				throw $e;
			}

			try {
				$guzzleOptions = \Pd\Monitoring\Check\Consumers\Client\Configuration::create(self::TIMEOUT, 2 * self::TIMEOUT);
				$client = new \GuzzleHttp\Client($guzzleOptions->config());

				$gradeResponse = \Nette\Utils\Json::decode((string) $client->get($check->getSslLabsApiLink())->getBody(), \Nette\Utils\Json::FORCE_ARRAY);

				if ($gradeResponse['status'] === 'READY') {
					$check->lastGrade = NULL;
					foreach ($gradeResponse['endpoints'] as $endpoint) {
						if (
							$check->lastGrade
							&&
							(
								! \in_array($endpoint['grade'], \Pd\Monitoring\Check\CertificateCheck::GRADES, TRUE)
								||
								\array_search($endpoint['grade'], \Pd\Monitoring\Check\CertificateCheck::GRADES, TRUE) < \array_search($check->lastGrade, \Pd\Monitoring\Check\CertificateCheck::GRADES, TRUE)
							)

						) {
							continue;
						}

						$check->lastGrade = $endpoint['grade'];
					}
				}
			} catch (\Exception $e) {
				$check->lastGrade = NULL;

				throw $e;
			}
		} catch (\Exception $e) {
			return FALSE;
		}

		return TRUE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_CERTIFICATE;
	}

}
