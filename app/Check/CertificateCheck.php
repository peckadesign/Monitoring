<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property int $daysBeforeWarning
 * @property \DateTime|NULL $lastValiddate
 * @property string|NULL $grade
 * @property string|NULL $lastGrade
 */
class CertificateCheck extends Check
{

	public const GRADE_AP = 'A+';
	public const GRADE_A = 'A';
	public const GRADE_AM = 'A-';
	public const GRADE_B = 'B';
	public const GRADE_C = 'C';
	public const GRADE_D = 'D';
	public const GRADE_E = 'E';
	public const GRADE_F = 'F';
	public const GRADE_T = 'T';
	public const GRADE_M = 'M';

	public const GRADES = [
		self::GRADE_AP,
		self::GRADE_A,
		self::GRADE_AM,
		self::GRADE_B,
		self::GRADE_C,
		self::GRADE_D,
		self::GRADE_E,
		self::GRADE_F,
		self::GRADE_T,
		self::GRADE_M,
	];


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_CERTIFICATE;
	}


	protected function getStatus(): int
	{
		if ( ! $this->lastValiddate) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->lastValiddate < (new \DateTime())->modify('+' . $this->daysBeforeWarning . ' days')) {
				return ICheck::STATUS_ALERT;
			}
		}

		if ( ! $this->lastGrade && $this->grade) {
			return ICheck::STATUS_ERROR;
		} elseif ($this->grade) {
			if (array_search($this->lastGrade, \Pd\Monitoring\Check\CertificateCheck::GRADES) > array_search($this->grade, \Pd\Monitoring\Check\CertificateCheck::GRADES)) {
				return ICheck::STATUS_ALERT;
			}
		}

		return ICheck::STATUS_OK;
	}


	public function getTitle(): string
	{
		return 'Nastavení HTTPS certifikátu';
	}


	public function getterStatusMessage(): string
	{
		$statusMessage = [];
		if ($this->lastValiddate && $this->lastValiddate < (new \DateTime())->modify('+' . $this->daysBeforeWarning . ' days')) {
			$statusMessage[] = $this->lastValiddate ? sprintf('Vyprší %s.', \Pd\Monitoring\Utils\Helpers::dateTime($this->lastValiddate)) : 'Nepodařilo se zjistit platnost certifikátu.';
		}
		if ($this->grade) {
			$statusMessage[] = $this->lastGrade ? sprintf('Známka SSL Labs je %s, očekává se alespoň %s.', $this->lastGrade, $this->grade) : 'Nepodařilo se zjistit známku na SSL Labs';
		}

		return implode(' ', $statusMessage);
	}


	protected function getDecayTimeout(): string
	{
		return 'PT24H';
	}

}
