<?php

namespace Pd\Monitoring\Check;

/**
 * @property int $id {primary}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$checks}
 * @property SlackNotifyLock $locks {1:m SlackNotifyLock::$check}
 * @property int $type {enum ICheck::TYPE_*}
 * @property int $status {virtual}
 * @property \DateTime|NULL $lastCheck
 * @property bool $paused {default TRUE}
 * @property string|NULL $name
 * @property string $fullName {virtual}
 * @property string $statusMessage {virtual}
 */
abstract class Check extends \Nextras\Orm\Entity\Entity implements
	ICheck
{

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function injectDateTimeProvider(\Kdyby\Clock\IDateTimeProvider $dateTimeProvider)
	{
		$this->dateTimeProvider = $dateTimeProvider;
	}


	public function __construct()
	{
		parent::__construct();

		$this->status = ICheck::STATUS_ERROR;
	}


	public function getType(): int
	{
		return $this->type;
	}


	public function getterFullName()
	{
		if ($this->name) {
			return $this->getTitle() . ' (' . $this->name . ')';
		} else {
			return $this->getTitle();
		}
	}


	public function getProducerName(): string
	{
		return lcfirst((new \ReflectionClass($this))->getShortName());
	}


	public function getterStatus(): int
	{
		if ( ! $this->lastCheck) {
			return ICheck::STATUS_ERROR;
		}
		if ($this->lastCheck < $this->dateTimeProvider->getDateTime()->sub(new \DateInterval($this->getDecayTimeout()))) {
			return ICheck::STATUS_ALERT;
		}

		return $this->getStatus();
	}


	protected function getDecayTimeout(): string
	{
		return 'PT1H';
	}


	abstract protected function getStatus(): int;


	abstract public function getterStatusMessage(): string;

}
