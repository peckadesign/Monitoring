<?php

namespace Pd\Monitoring\User;

use Nette;
use Nextras;


/**
 * @property int $id {primary}
 * @property int $gitHubId
 * @property string $gitHubName
 * @property string $gitHubToken
 * @property bool $administrator
 */
class User extends Nextras\Orm\Entity\Entity implements Nette\Security\IIdentity
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
