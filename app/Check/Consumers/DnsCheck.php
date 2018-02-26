<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class DnsCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\DnsCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->lastDnsValue = NULL;

		$mapping = [
			\Pd\Monitoring\Check\DnsCheck::DNS_TYPE_A => DNS_A,
			\Pd\Monitoring\Check\DnsCheck::DNS_TYPE_MX => DNS_MX,
			\Pd\Monitoring\Check\DnsCheck::DNS_TYPE_TXT => DNS_TXT,
		];

		$internalDnsType = $mapping[$check->dnsType];
		$entries = dns_get_record($check->url, $internalDnsType);
		if ( ! $entries) {
			return FALSE;
		}

		switch ($internalDnsType) {
			case DNS_A:
				$cb = function (array $parts) {
					return $parts['ip'];
				};
				break;
			case DNS_MX:
				$cb = function (array $parts) {
					return $parts['target'];
				};
				break;
			case DNS_TXT:
				$cb = function (array $parts) {
					return $parts['txt'];
				};
				break;
			default:
				return FALSE;
		}

		$entries = array_map($cb, $entries);
		$check->lastDnsValue = implode(';', $entries);

		return TRUE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_DNS;
	}
}
