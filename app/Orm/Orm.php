<?php

namespace Pd\Monitoring\Orm;

use Pd;
use Nextras;


/**
 * @property-read Pd\Monitoring\User\UsersRepository $users
 * @property-read Pd\Monitoring\Project\ProjectsRepository $projects
 * @property-read Pd\Monitoring\Check\ChecksRepository $checks
 * @property-read Pd\Monitoring\Check\SlackNotifyLocksRepository $slackNotifyLocks
 * @property-read Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProjectRepository $usersFavoriteProjectsRepository
 */
class Orm extends Nextras\Orm\Model\Model
{

}
