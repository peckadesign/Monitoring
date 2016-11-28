<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $message
 * @property \DateTime $term
 */
class TermCheck extends Check
{

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_TERM;
	}


	public function injectDateTimeProvider(\Kdyby\Clock\IDateTimeProvider $dateTimeProvider)
	{
		$this->dateTimeProvider = $dateTimeProvider;
	}


	public function getTitle(): string
	{
		return 'Upozornění na termín';
	}


	function getterStatus(): int
	{
		if ($this->dateTimeProvider->getDateTime() > $this->term) {
			return ICheck::STATUS_ERROR;
		} elseif ($this->dateTimeProvider->getDateTime()->add(new \DateInterval('P1W')) > $this->term) {
			return ICheck::STATUS_ALERT;
		} else {
			return ICheck::STATUS_OK;
		}
	}
}
