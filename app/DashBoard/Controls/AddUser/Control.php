<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddUser;

class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\DashBoard\Forms\UserAddEditFormFactory $userAddEditFormFactory;

	private \Pd\Monitoring\User\UsersRepository $usersRepository;

	private \Nette\Security\User $user;

	private \Nette\Security\Passwords $passwords;


	public function __construct(
		//\Pd\Monitoring\User\User $identity,
		\Pd\Monitoring\DashBoard\Forms\UserAddEditFormFactory $userAddEditFormFactory,
		\Pd\Monitoring\User\UsersRepository $usersRepository,
		\Nette\Security\User $user,
		\Nette\Security\Passwords $passwords
	)
	{
		$this->userAddEditFormFactory = $userAddEditFormFactory;
		$this->usersRepository = $usersRepository;
		$this->user = $user;
		$this->passwords = $passwords;
	}


	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/Control.latte');
		$this->getTemplate()->render();
	}


	protected function createComponentForm(): \Nette\Application\UI\Form
	{
		$form = $this->userAddEditFormFactory->create();

		$form->getComponent(\Pd\Monitoring\DashBoard\Forms\UserAddEditFormFactory::FIELD_PASSWORD)
			->setRequired();

		$form->onSuccess[] = function (\Nette\Forms\Form $form, \Pd\Monitoring\DashBoard\Forms\UserEditFormData $values): void
		{
			$this->processAddForm($form, $values);
		};

		return $form;
	}


	protected function processAddForm(\Nette\Forms\Form $form, \Pd\Monitoring\DashBoard\Forms\UserEditFormData $values): void
	{

		$user = new \Pd\Monitoring\User\User();
		$user->gitHubName = $values->gitHubName;
		$user->slackId = $values->slackId;

		/** @var bool $administrator */
		$administrator = $values->administrator;
		$user->administrator = $administrator;

		$user->email = $values->email;

		/** @var string $password */
		$password = $values->password;
		$user->password = $this->passwords->hash($password);

		$this->usersRepository->persistAndFlush($user);

		if ($this->user->isAllowed(\Pd\Monitoring\User\AclFactory::RESOURCE_USER, \Pd\Monitoring\User\AclFactory::PRIVILEGE_ADD)) {
			$this->getPresenter()->redirect(':DashBoard:User:');
		} else {
			$this->getPresenter()->redirect(':DashBoard:HomePage:');
		}
	}

}
