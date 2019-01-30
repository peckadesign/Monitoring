<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property string $xpath
 * @property int $operator {enum self::OPERATOR_*}
 * @property string $xpathResult
 * @property string|null $xpathLastResult
 */
final class XpathCheck extends Check
{

	public const ALIVE_TIMEOUT = 5000;

	public const OPERATOR_LT = 0;
	public const OPERATOR_GT = 1;
	public const OPERATOR_EQ = 2;
	public const OPERATOR_MATCH = 3;

	public const OPERATORS = [
		self::OPERATOR_LT => 'počet menší než',
		self::OPERATOR_GT => 'počet větší než',
		self::OPERATOR_EQ => 'počet roven',
		self::OPERATOR_MATCH => 'je shodný',
	];


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_XPATH;
	}


	protected function getStatus(): int
	{
		$return = FALSE;

		if ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_MATCH) {
			$return = $this->xpathLastResult == $this->xpathResult;
		} elseif ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
			$return = $this->xpathLastResult < $this->xpathResult;
		} elseif ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_EQ) {
			$return = $this->xpathLastResult == $this->xpathResult;
		} elseif ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
			$return = $this->xpathLastResult > $this->xpathResult;
		}

		return $return ? ICheck::STATUS_OK : ICheck::STATUS_ERROR;
	}


	public function getTitle(): string
	{
		return 'XPath';
	}


	public function getterStatusMessage(): string
	{
		if ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_MATCH) {
			$return = 'má být shodný s';
		} elseif ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
			$return = 'má být menší než';
		} elseif ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_EQ) {
			$return = 'má být roven';
		} elseif ($this->operator === \Pd\Monitoring\Check\XpathCheck::OPERATOR_LT) {
			$return = 'mát být větší než';
		}

		return \sprintf("Očekávaný výsledek '%s %s' a je '%s'", $return, $this->xpathResult, $this->xpathLastResult);
	}

}
