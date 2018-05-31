<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property string $dnsType {enum self::DNS_TYPE_*}
 * @property string $dnsValue
 * @property string|NULL $lastDnsValue
 */
class DnsCheck extends Check
{

	public const DNS_TYPE_A = 'A';
	public const DNS_TYPE_MX = 'MX';
	public const DNS_TYPE_TXT = 'TXT';

	public static $dnsTypes = [
		self::DNS_TYPE_A,
		self::DNS_TYPE_MX,
		self::DNS_TYPE_TXT,
	];


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_DNS;
	}


	protected function getStatus(): int
	{
		if ($this->lastDnsValue && \in_array($this->dnsType, [self::DNS_TYPE_A, self::DNS_TYPE_MX, self::DNS_TYPE_TXT], TRUE)) {
			$lastDnsValue = \explode(';', $this->lastDnsValue);
			\sort($lastDnsValue);

			$dnsValue = \explode(';', $this->dnsValue);
			\sort($dnsValue);

			return $lastDnsValue != $dnsValue ? ICheck::STATUS_ALERT : ICheck::STATUS_OK;
		}

		return ICheck::STATUS_ERROR;
	}


	public function getTitle(): string
	{
		return 'Nastavení DNS';
	}


	public function getterStatusMessage(): string
	{
		return $this->getStatus() === ICheck::STATUS_ALERT ? \sprintf('Očekávaná hodnota DNS %s záznamu "%s" neodpovídá zjištěnému "%s"', $this->dnsType, $this->dnsValue, $this->lastDnsValue) : '';
	}


	public function getterFullName(): string
	{
		$name = parent::getterFullName();

		return $name . ' ' . $this->dnsType;
	}

}
