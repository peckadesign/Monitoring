<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserProjectNotifications;

/**
 * @property array $id {primary-proxy}
 * @property \Pd\Monitoring\User\User $user {m:1 \Pd\Monitoring\User\User::$userProjectNotifications} {primary}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$userProjectNotifications} {primary}
 */
class UserProjectNotifications extends \Nextras\Orm\Entity\Entity
{

}
