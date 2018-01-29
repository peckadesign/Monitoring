<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

/**
 * @property int $id {primary}
 * @property int $gitHubId
 * @property string $gitHubName
 * @property string $gitHubToken
 * @property bool $administrator
 * @property string|null $slackId
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject[] $favoriteProjects {1:m \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject::$user}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UserSlackNotifications\UserSlackNotifications[] $userSlackNotifications {1:m \Pd\Monitoring\UserSlackNotifications\UserSlackNotifications::$user}
 */
class User extends \Nextras\Orm\Entity\Entity implements \Nette\Security\IIdentity
{

	public function getId(): int
	{
		return $this->id;
	}


	public function getRoles(): array
	{
		$roles = ['user'];

		if ($this->administrator) {
			$roles[] = 'administrator';
		}

		return $roles;
	}
}
