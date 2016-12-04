<?php

namespace Pd\Monitoring\Check;

/**
 * @property int $id {primary}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$checks}
 * @property int $type {enum ICheck::TYPE_*}
 * @property int $status {virtual}
 * @property \DateTime|NULL $lastCheck
 * @property bool $paused {default TRUE}
 * @property string|NULL $name
 * @property string $fullName {virtual}
 */
abstract class Check extends \Nextras\Orm\Entity\Entity implements
	ICheck
{

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


	abstract function getterStatus(): int;

}
