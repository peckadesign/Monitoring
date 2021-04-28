<?php declare(strict_types = 1);

namespace Pd\Monitoring\Orm;

/**
 * @property-read \Pd\Monitoring\User\UsersRepository $users
 * @property-read \Pd\Monitoring\Project\ProjectsRepository $projects
 * @property-read \Pd\Monitoring\Check\ChecksRepository $checks
 * @property-read \Pd\Monitoring\Check\SlackNotifyLocksRepository $slackNotifyLocks
 * @property-read \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository
 * @property-read \Pd\Monitoring\UserProjectNotifications\UserProjectNotificationsRepository $userProjectNotificationsRepository
 * @property-read \Pd\Monitoring\UserCheckNotifications\UserCheckNotificationsRepository $userCheckNotificationsRepository
 * @property-read \Pd\Monitoring\UserOnProject\UserOnProjectRepository $userOnProjectRepository
 */
class Orm extends \Nextras\Orm\Model\Model
{

}
