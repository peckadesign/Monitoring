<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

class AclFactory
{

	private UsersRepository $usersRepository;


	public function __construct(UsersRepository $usersRepository)
	{
		$this->usersRepository = $usersRepository;
	}


	public function create(): \Nette\Security\Authorizator
	{
		$authorizator = new \Nette\Security\Permission();

		$authorizator->addResource('project');
		$authorizator->addResource('check');
		$authorizator->addResource('user');

		$authorizator->addRole('user');
		$authorizator->addRole('administrator', 'user');

		$authorizator->allow('user', \Nette\Security\Authorizator::ALL, 'view');
		$authorizator->allow('administrator');

		foreach ($this->usersRepository->findAll() as $user) {
			$authorizator->addRole('user' . $user->getId());
			$authorizator->addResource('password' . $user->getId());
			$authorizator->allow('user' . $user->getId(), 'password' . $user->getId(), \Nette\Security\Authorizator::ALL);
		}

		return $authorizator;
	}

}
