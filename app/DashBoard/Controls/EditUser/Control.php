<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\EditUser;

class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\DashBoard\Forms\UserEditFormFactory $userEditFormFactory;

	private \Pd\Monitoring\User\User $identity;

	private \Pd\Monitoring\User\UsersRepository $usersRepository;

	private \Nette\Security\User $user;

	private \Nette\Security\Passwords $passwords;


	public function __construct(
		\Pd\Monitoring\User\User $identity,
		\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory $userEditFormFactory,
		\Pd\Monitoring\User\UsersRepository $usersRepository,
		\Nette\Security\User $user,
		\Nette\Security\Passwords $passwords
	)
	{
		$this->identity = $identity;
		$this->userEditFormFactory = $userEditFormFactory;
		$this->usersRepository = $usersRepository;
		$this->user = $user;
		$this->passwords = $passwords;

		$this->onAnchor[] = function (\Nette\Application\UI\Control $control): void
		{
			$this['form']->setDefaults($this->identity->toArray());
		};
	}


	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	protected function createComponentForm(): \Nette\Application\UI\Form
	{
		$form = $this->userEditFormFactory->create();

		if (!$this->user->isAllowed($this->identity, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			$form->removeComponent($form->getComponent(\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_PASSWORD));
		}

		$form->onSuccess[] = function (\Nette\Forms\Form $form, \Pd\Monitoring\DashBoard\Forms\UserEditFormData $values): void
		{
			$this->processEditForm($form, $values);
		};

		return $form;
	}


	protected function processEditForm(\Nette\Forms\Form $form, \Pd\Monitoring\DashBoard\Forms\UserEditFormData $values): void
	{
		$this->identity->gitHubName = $values->gitHubName;
		$this->identity->slackId = $values->slackId;
		$this->identity->email = $values->email;

		if (
			$this->user->isAllowed($this->identity, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)
			&&
			$values->password !== NULL
		) {
			$this->identity->password = $this->passwords->hash($values->password);
		}

		if (
			$this->user->isAllowed(\Pd\Monitoring\User\AclFactory::RESOURCE_USER, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)
			&&
			$values->administrator !== NULL
		) {
			$this->identity->administrator = $values->administrator;
		}

		$this->usersRepository->persistAndFlush($this->identity);

		if ($this->user->isAllowed(\Pd\Monitoring\User\AclFactory::RESOURCE_USER, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			$this->getPresenter()->redirect(':DashBoard:User:');
		} else {
			$this->getPresenter()->redirect(':DashBoard:HomePage:');
		}
	}

}
