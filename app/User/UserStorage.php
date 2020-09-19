<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

class UserStorage extends \Nette\Http\UserStorage
{

	private ?User $user = NULL;

	private \Pd\Monitoring\Orm\Orm $orm;


	public function __construct(
		\Nette\Http\Session $sessionHandler,
		\Pd\Monitoring\Orm\Orm $orm
	)
	{
		parent::__construct($sessionHandler);

		$this->orm = $orm;
	}


	public function setIdentity(?\Nette\Security\IIdentity $user = NULL)
	{
		if ($user && ! $user instanceof User) {
			throw new \InvalidArgumentException('Expected instance of ' . User::class);
		}

		$this->user = $user;

		return parent::setIdentity($user ? new \Nette\Security\Identity($user->getId()) : NULL);
	}


	public function getIdentity(): ?\Nette\Security\IIdentity
	{
		$identity = parent::getIdentity();
		if ( ! $identity) {
			return NULL;
		}

		if ( ! $this->user) {
			$this->user = $this->orm->users->getById($identity->getId());
		}

		return $this->user;
	}

}
