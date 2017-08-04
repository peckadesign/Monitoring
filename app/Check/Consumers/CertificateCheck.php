<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class CertificateCheck extends Check
{

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider,
		\Pd\Monitoring\Orm\Orm $orm,
		\Monolog\Logger $logger
	) {
		parent::__construct($checksRepository, $dateTimeProvider, $orm, $logger);

		$this->dateTimeProvider = $dateTimeProvider;
	}


	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\CertificateCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		try {
			$get = stream_context_create(["ssl" => ["capture_peer_cert" => TRUE]]);
			$read = stream_socket_client("ssl://" . $check->url . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
			$cert = stream_context_get_params($read);
			$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate'], TRUE);

			if (empty($certinfo['validTo_time_t'])) {
				throw new \InvalidArgumentException('No certificate data');
			}

			$date = $this->dateTimeProvider->getDateTime();
			$date = $date->setTimestamp($certinfo['validTo_time_t']);

			$check->lastValiddate = $date;
		} catch (\Exception $e) {
			$check->lastValiddate = NULL;

			return FALSE;
		}

		return TRUE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_CERTIFICATE;
	}
}
