<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property float|NULL $value
 * @property float $threshold
 * @property int $operator {enum self::OPERATOR_*}
 */
class NumberValueCheck extends Check
{

	public const OPERATOR_LT = 0;
	public const OPERATOR_GT = 1;
	public const OPERATOR_EQ = 2;

	public const OPERATORS = [
		self::OPERATOR_LT => 'menší než',
		self::OPERATOR_GT => 'větší než',
		self::OPERATOR_EQ => 'rovna',
	];


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_NUMBER_VALUE;
	}


	public function getStatus(): int
	{
		if ($this->value === NULL) {
			return ICheck::STATUS_ERROR;
		}

		if (
			$this->operator === self::OPERATOR_GT && $this->value < $this->threshold
			||
			$this->operator === self::OPERATOR_LT && $this->value > $this->threshold
			||
			$this->operator === self::OPERATOR_EQ && $this->value !== $this->threshold

		) {
			return ICheck::STATUS_ALERT;
		}

		return ICheck::STATUS_OK;
	}


	public function getTitle(): string
	{
		return 'Číselná hodnota';
	}


	public function getterStatusMessage(): string
	{
		$message = '';

		if ($this->getStatus() === ICheck::STATUS_ALERT) {
			$originalPrecision = ($decimalPosition = \strpos((string) $this->threshold, '.')) !== FALSE ? \strlen(\substr((string) $this->threshold, $decimalPosition + 1)) : 0;

			$message = \sprintf('Očekávaná hodnota musí být %s %.' . $originalPrecision . 'f a je %.' . $originalPrecision . 'f.', NumberValueCheck::OPERATORS[$this->operator], \round($this->threshold, $originalPrecision), \round($this->value, $originalPrecision));
		} elseif ($this->getStatus() === ICheck::STATUS_ERROR) {
			$message = 'Očekávaná hodnota není známa.';
		}

		return $message;
	}

}
