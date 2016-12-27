<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int $daysBeforeWarning
 * @property \DateTime|NULL $lastValiddate
 */
class CertificateCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_CERTIFICATE;
	}


	public function getterStatus(): int
	{
		if ( ! $this->lastValiddate) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->lastValiddate >= (new \DateTime())->modify('+' . $this->daysBeforeWarning . ' days')) {
				return ICheck::STATUS_OK;
			} else {
				return ICheck::STATUS_ALERT;
			}
		}
	}


	public function getTitle(): string
	{
		return 'Platnost HTTPS certifikátu';
	}
}
