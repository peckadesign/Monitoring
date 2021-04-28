<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserOnProject;

/**
 * @property array $id {primary-proxy}
 * @property \Pd\Monitoring\User\User $user {m:1 \Pd\Monitoring\User\User::$favoriteProjects} {primary}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$favoriteProjects} {primary}
 * @property bool $view
 * @property bool $edit
 * @property bool $admin
 */
class UserOnProject extends \Nextras\Orm\Entity\Entity
{

}
