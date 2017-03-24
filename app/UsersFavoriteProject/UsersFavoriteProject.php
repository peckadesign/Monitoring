<?php declare(strict_types = 1);

namespace Pd\Monitoring\UsersFavoriteProject;

/**
 * @property array $id {primary-proxy}
 * @property \Pd\Monitoring\User\User $user {m:1 \Pd\Monitoring\User\User::$favoriteProjects} {primary}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$favoriteProjects} {primary}
 */
class UsersFavoriteProject extends \Nextras\Orm\Entity\Entity
{

}
