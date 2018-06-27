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
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UserSlackNotifications\UserSlackNotifications[] $userSlackNotifications {1:m \Pd\Monitoring\UserSlackNotifications\UserSlackNotifications::$project}
 */
class Project extends \Nextras\Orm\Entity\Entity
{

}
