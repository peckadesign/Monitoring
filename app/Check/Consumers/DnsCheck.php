<?php declare(strict_types=1);

namespace Pd\Monitoring\Check\Consumers;

class DnsCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\DnsCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$check->lastIp = NULL;
		$process = new \Symfony\Component\Process\Process(sprintf('/usr/bin/dig %s A +short', $check->url));
		try {
			$process->mustRun();
			$check->lastIp = trim($process->getOutput());
		} catch (\Symfony\Component\Process\Exception\ProcessFailedException $e) {
			return FALSE;
		}

		return TRUE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_DNS;
	}
}
