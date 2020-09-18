<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property int $id {primary}
 * @property string $url
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$checks}
 * @property SlackNotifyLock $locks {1:m SlackNotifyLock::$check, cascade=[persist, remove]}
 * @property int $type {enum ICheck::TYPE_*}
 * @property int $status {virtual}
 * @property \DateTimeImmutable|NULL $lastCheck
 * @property bool $paused {default TRUE}
 * @property string|NULL $name
 * @property string $fullName {virtual}
 * @property string $statusMessage {virtual}
 * @property bool $onlyErrors {default FALSE}
 * @property string|NULL $pausedFrom
 * @property string|NULL $pausedTo
 * @property bool $reference {default FALSE}
 * @property bool $siteMap {default FALSE}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UserCheckNotifications\UserCheckNotifications[] $userCheckNotifications {1:m \Pd\Monitoring\UserCheckNotifications\UserCheckNotifications::$check}
 */
abstract class Check extends \Nextras\Orm\Entity\Entity implements
	ICheck
{

	/**
	 * @var \Pd\Monitoring\Utils\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function injectBaseDateTimeProvider(\Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider): void
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


	public function getterFullName(): string
	{
		if ($this->name) {
			return $this->getTitle() . ' (' . $this->name . ')';
		} else {
			return $this->getTitle();
		}
	}


	public function getProducerName(): string
	{
		return \lcfirst((new \ReflectionClass($this))->getShortName());
	}


	public function getterStatus(): int
	{
		if ( ! $this->lastCheck) {
			return ICheck::STATUS_ERROR;
		}

		$status = $this->getStatus();

		if ($status < ICheck::STATUS_ALERT && ICheck::STATUS_ALERT && $this->lastCheck < $this->dateTimeProvider->getDateTime()->sub(new \DateInterval($this->getDecayTimeout()))) {
			return ICheck::STATUS_ALERT;
		}

		return $status;
	}


	protected function getDecayTimeout(): string
	{
		return 'PT1H';
	}


	abstract protected function getStatus(): int;


	abstract public function getterStatusMessage(): string;


	public function isPaused(): bool
	{
		if ($this->paused) {
			return TRUE;
		}

		return \Pd\Monitoring\Utils\Helpers::isInTimeInterval($this->dateTimeProvider->getDateTime(), $this->pausedFrom, $this->pausedTo);
	}

}
