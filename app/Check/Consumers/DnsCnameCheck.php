<?php declare(strict_types=1);

namespace Pd\Monitoring\Check\Consumers;

class DnsCnameCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\DnsCnameCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->lastTarget = NULL;

		$entries = dns_get_record($check->url, DNS_CNAME);
		if (!$entries) {
			return FALSE;
		}

		$entry = array_shift($entries);
		$check->lastTarget = $entry['target'];
		return TRUE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_DNS_CNAME;
	}
}
