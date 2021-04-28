<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

/**
 * @property int $id {primary}
 * @property int $gitHubId
 * @property string $gitHubName
 * @property string $gitHubToken
 * @property bool $administrator
 * @property string|null $slackId
 * @property string|null $password
 * @property string|null $authtoken
 * @property string|null $email
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject[] $favoriteProjects {1:m \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject::$user}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UserProjectNotifications\UserProjectNotifications[] $userProjectNotifications {1:m \Pd\Monitoring\UserProjectNotifications\UserProjectNotifications::$user}
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\UserCheckNotifications\UserCheckNotifications[] $userCheckNotifications {1:m \Pd\Monitoring\UserCheckNotifications\UserCheckNotifications::$user}
 */
class User extends \Nextras\Orm\Entity\Entity implements \Nette\Security\IIdentity, \Nette\Security\Role, \Nette\Security\Resource
{

	public function getId(): int
	{
		return $this->id;
	}


	public function getRoles(): array
	{
		$roles = [\Pd\Monitoring\User\AclFactory::ROLE_USER, $this->getRoleId()];

		if ($this->administrator) {
			$roles[] = \Pd\Monitoring\User\AclFactory::ROLE_ADMINISTRATOR;
		}

		return $roles;
	}


	public function getRoleId(): string
	{
		return \Pd\Monitoring\User\AclFactory::ROLE_USER . $this->id;
	}


	public function getResourceId(): string
	{
		return self::createResourceId($this->id);
	}


	public static function createResourceId(int $id): string
	{
		return \Pd\Monitoring\User\AclFactory::ROLE_USER . $id;
	}


	public function __toString(): string
	{
		return (string) $this->id;
	}

}
