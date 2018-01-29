<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

trait TSecuredPresenter
{

	/**
	 * @var \Nette\Security\Permission
	 */
	private $authorizator;


	public function injectAuthorizator(\Nette\Security\IAuthorizator $authorizator): void
	{
		$this->authorizator = $authorizator;
	}


	public function checkRequirements($element)
	{
		parent::checkRequirements($element);

		if ( ! $this->user->loggedIn) {
			$this->redirect(':DashBoard:Login:default', ['backLink' => $this->storeRequest()]);
		}

		$acl = (array) \Nette\Application\UI\ComponentReflection::parseAnnotation($element, 'Acl');
		if (count($acl) && reset($acl) !== FALSE) {
			if (count($acl) !== 2) {
				throw new \InvalidArgumentException('Není uvedeno privilege');
			}
			$resource = $acl[0];
			$privilege = $acl[1];
			$this->authorizator->hasResource($resource);

			if ( ! $this->user->isAllowed($resource, $privilege)) {
				$message = sprintf(
					"Uživatel '%s' nemá oprávnění pro zdroj '%s'",
					$this->user->getIdentity()->gitHubName,
					$resource
				);
				throw new \Nette\Application\ForbiddenRequestException($message);
			}
		}
	}

}
