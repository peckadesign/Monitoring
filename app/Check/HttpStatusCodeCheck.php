<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $code
 * @property string|NULL $lastCode
 */
class HttpStatusCodeCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_HTTP_STATUS_CODE;
	}


	protected function getStatus(): int
	{
		if ( ! $this->lastCode) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->lastCode === $this->code) {
				return ICheck::STATUS_OK;
			} else {
				return ICheck::STATUS_ALERT;
			}
		}
	}


	public function getterStatusMessage(): string
	{
		if ($this->getStatus() === ICheck::STATUS_ERROR) {
			return 'Status kód není znám';
		} else {
			return 'Status kód je ' . $this->lastCode . ', očekávaný je ' . $this->code . '.';
		}
	}


	public function getTitle(): string
	{
		return 'HTTP stavový kód';
	}

}
