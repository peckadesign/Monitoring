<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

class AclFactory
{

	public function create(): \Nette\Security\IAuthorizator
	{
		$authorizator = new \Nette\Security\Permission();

		$authorizator->addResource('project');
		$authorizator->addResource('check');
		$authorizator->addResource('user');

		$authorizator->addRole('user');
		$authorizator->addRole('administrator', 'user');

		$authorizator->allow('user', \Nette\Security\IAuthorizator::ALL, 'view');
		$authorizator->allow('administrator');

		return $authorizator;
	}
}
