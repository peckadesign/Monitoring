<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard;

trait TSecuredComponent
{

	private \Nette\Security\Permission $authorizator;


	public function injectAuthorizator(\Nette\Security\IAuthorizator $authorizator): void
	{
		$this->authorizator = $authorizator;
	}


	public function checkRequirements($element): void
	{
		parent::checkRequirements($element);

		/** @var \Nette\Security\User $user */
		$user = $this->getPresenter()->getUser();

		if ( ! $user->isLoggedIn()) {
			$this->getPresenter()->redirect(':DashBoard:Login:default', ['backLink' => $this->getPresenter()->storeRequest()]);
		}

		$acl = (array) \Nette\Application\UI\ComponentReflection::parseAnnotation($element, 'Acl');
		if (\count($acl) && \reset($acl) !== FALSE) {
			if (\count($acl) !== 2) {
				throw new \InvalidArgumentException('Není uvedeno privilege');
			}
			$resource = $acl[0];
			$privilege = $acl[1];
			$this->authorizator->hasResource($resource);

			if ( ! $user->isAllowed($resource, $privilege)) {
				$message = \sprintf(
					"Uživatel '%s' nemá oprávnění pro zdroj '%s'",
					$user->getIdentity()->gitHubName,
					$resource
				);
				throw new \Nette\Application\ForbiddenRequestException($message);
			}
		}
	}

}
