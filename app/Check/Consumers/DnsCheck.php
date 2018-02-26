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
			case DNS_MX:
			case DNS_TXT:
				$cb = function (array $parts) {
					return array_pop($parts);
				};
				$entries = array_map($cb, $entries);
				$check->lastDnsValue = implode(';', $entries);
				break;
			default:
				return FALSE;
		}

		return TRUE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_DNS;
	}
}
