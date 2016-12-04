<?php

namespace Pd\Monitoring\Check\Consumers;

class CertificateCheck implements \Kdyby\RabbitMq\IConsumer
{

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		$this->checksRepository = $checksRepository;
	}


	public function process(\PhpAmqpLib\Message\AMQPMessage $message): int
	{
		$checkId = $message->getBody();

		/** @var \Pd\Monitoring\Check\DnsCheck $check */
		$check = $this->checksRepository->getById($checkId);

		if ( ! $check || ! $check instanceof \Pd\Monitoring\Check\CertificateCheck) {
			return self::MSG_REJECT;
		}

		$check->lastCheck = new \DateTime();

		try {
			$get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
			$read = stream_socket_client("ssl://". $check->url .":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
			$cert = stream_context_get_params($read);
			$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

			if (empty($certinfo['validTo_time_t'])) {
				throw new \InvalidArgumentException('No certificate data');
			}

			$date = new \DateTime();
			$date->setTimestamp($certinfo['validTo_time_t']);

			$check->lastValiddate = $date;
		} catch (\Exception $e) {
			$check->lastValiddate = NULL;
		}

		$this->checksRepository->persistAndFlush($check);

		return self::MSG_ACK;
	}
}
