<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserCheckNotifications;

/**
 * @property array $id {primary-proxy}
 * @property \Pd\Monitoring\User\User $user {m:1 \Pd\Monitoring\User\User::$userCheckNotifications} {primary}
 * @property \Pd\Monitoring\Check\Check $check {m:1 \Pd\Monitoring\Check\Check::$userCheckNotifications} {primary}
 */
class UserCheckNotifications extends \Nextras\Orm\Entity\Entity
{

}
