<?php declare(strict_types = 1);

namespace Pd\Monitoring\Project;

/**
 * @property int $id {primary}
 * @property string $name
 * @property string $url
 * @property \DateTimeImmutable|NULL $maintenance
 * @property string|NULL $pausedFrom
 * @property string|NULL $pausedTo
 * @property bool $notifications {default TRUE}
 * @property Project|null $parent {m:1 Project::$subProjects}
 * @property bool $reference {default FALSE}
 * @property \Nextras\Orm\Relationships\OneHasMany|Project[] $subProjects {1:m Project::$parent}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\Check\Check[] $checks {1:m \Pd\Monitoring\Check\Check::$project}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject[] $favoriteProjects {1:m \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject::$project}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UserProjectNotifications\UserProjectNotifications[] $userProjectNotifications {1:m \Pd\Monitoring\UserProjectNotifications\UserProjectNotifications::$project}
 * @property \Nextras\Orm\Relationships\ManyHasMany|\Pd\Monitoring\Slack\Integration[] $slackIntegrations {m:m \Pd\Monitoring\Slack\Integration, isMain=true, oneSided=true}
 */
class Project extends \Nextras\Orm\Entity\Entity implements \Nette\Security\Resource
{

	private \Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider;


	public function injectBaseDateTimeProvider(\Pd\Monitoring\Utils\IDateTimeProvider $dateTimeProvider): void
	{
		$this->dateTimeProvider = $dateTimeProvider;
	}


	public function isPaused(): bool
	{
		return \Pd\Monitoring\Utils\Helpers::isInTimeInterval($this->dateTimeProvider->getDateTime(), $this->pausedFrom, $this->pausedTo);
	}


	public function getResourceId(): string
	{
		return 'project' . $this->id;
	}

}
