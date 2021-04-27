<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

class AclFactory
{

	public const RESOURCE_PROJECT = 'project';
	public const RESOURCE_CHECK = 'check';
	public const RESOURCE_USER = 'user';

	public const ROLE_USER = 'user';
	public const ROLE_ADMINISTRATOR = 'administrator';

	public const PRIVILEGE_ADD = 'add';
	public const PRIVILEGE_VIEW = 'view';
	public const PRIVILEGE_EDIT = 'edit';
	public const PRIVILEGE_DELETE = 'delete';


	private UsersRepository $usersRepository;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;


	public function __construct(
		UsersRepository $usersRepository,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository
	)
	{
		$this->usersRepository = $usersRepository;
		$this->projectsRepository = $projectsRepository;
	}


	public function create(): \Nette\Security\Authorizator
	{
		$permission = new \Nette\Security\Permission();

		$permission->addResource(\Pd\Monitoring\User\AclFactory::RESOURCE_PROJECT);
		$permission->addResource(\Pd\Monitoring\User\AclFactory::RESOURCE_CHECK);
		$permission->addResource(\Pd\Monitoring\User\AclFactory::RESOURCE_USER);

		$permission->addRole(\Pd\Monitoring\User\AclFactory::ROLE_USER);
		$permission->addRole(\Pd\Monitoring\User\AclFactory::ROLE_ADMINISTRATOR);

		foreach ($this->projectsRepository->findAll() as $project) {
			$permission->addResource($project->getResourceId());
			$permission->allow(\Pd\Monitoring\User\AclFactory::ROLE_USER, $project->getResourceId(), \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW);
		}

		foreach ($this->usersRepository->findAll() as $user) {
			$permission->addRole($user->getRoleId());
			$permission->addResource($user->getResourceId());
			$permission->allow($user->getRoleId(), $user->getResourceId(), \Nette\Security\Authorizator::ALL);
		}

		$permission->allow(\Pd\Monitoring\User\AclFactory::ROLE_ADMINISTRATOR, \Nette\Security\Authorizator::ALL, \Nette\Security\Authorizator::ALL);

		return $permission;
	}

}
