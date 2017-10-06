<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserSlackNotifications;

/**
 * @property array $id {primary-proxy}
 * @property \Pd\Monitoring\User\User $user {m:1 \Pd\Monitoring\User\User::$userSlackNotifications} {primary}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$userSlackNotifications} {primary}
 */
class UserSlackNotifications extends \Nextras\Orm\Entity\Entity
{

}
