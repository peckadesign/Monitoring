<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property string $ip
 * @property string|NULL $lastIp
 */
class DnsCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_DNS;
	}


	public function getterStatus(): int
	{
		if ($this->lastIp === $this->ip) {
			return ICheck::STATUS_OK;
		} else {
			return ICheck::STATUS_ERROR;
		}
	}


	public function getTitle(): string
	{
		return 'Nastavení DNS';
	}
}
