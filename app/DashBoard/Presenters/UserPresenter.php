<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

class UserPresenter extends BasePresenter
{

	private \Pd\Monitoring\DashBoard\Controls\EditUser\IFactory $editUserControlFactory;

	private \Pd\Monitoring\User\User $editedUser;

	private \Pd\Monitoring\DashBoard\Controls\UserList\IFactory $userListControlFactory;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\EditUser\IFactory $editUserControlFactory,
		\Pd\Monitoring\DashBoard\Controls\UserList\IFactory $userListControlFactory
	)
	{
		parent::__construct();
		$this->editUserControlFactory = $editUserControlFactory;
		$this->userListControlFactory = $userListControlFactory;
	}


	public function actionDefault(): void
	{
		if ( ! $this->user->isAllowed(\Pd\Monitoring\User\AclFactory::RESOURCE_USER, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}
	}


	public function actionEdit(\Pd\Monitoring\User\User $user): void
	{
		\Tracy\Debugger::barDump($user);
		if ( ! $this->user->isAllowed($user, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		$this->editedUser = $user;
	}


	protected function createComponentEditUser(): \Nette\Application\UI\Control
	{
		return $this->editUserControlFactory->create($this->editedUser);
	}


	protected function createComponentUserList(): \Nette\Application\UI\Control
	{
		return $this->userListControlFactory->create();
	}

}
