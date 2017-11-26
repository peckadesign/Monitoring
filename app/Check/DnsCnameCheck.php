<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property string $target
 * @property string|NULL $lastTarget
 */
class DnsCnameCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_DNS_CNAME;
	}


	protected function getStatus(): int
	{
		if ($this->lastTarget === $this->target) {
			return ICheck::STATUS_OK;
		} else {
			return ICheck::STATUS_ERROR;
		}
	}


	public function getTitle(): string
	{
		return 'Nastavení DNS CNAME';
	}


	public function getterStatusMessage(): string
	{
		return $this->lastTarget !== $this->target ? sprintf('Očekávaná CNAME adresa "%s" neodpovídá zjištěné "%s"', $this->target, $this->lastTarget) : '';
	}
}
