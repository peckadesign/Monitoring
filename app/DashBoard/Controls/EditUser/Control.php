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

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $values)
		{
			$this->processEditForm($form, $values);
		};

		return $form;
	}


	protected function processEditForm(\Nette\Forms\Form $form, array $values): void
	{
		$this->identity->gitHubName = $values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_GIT_HUB_NAME];
		$this->identity->slackId = $values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_SLACK_ID];
		$this->identity->email = $values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_EMAIL];

		if ($values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_PASSWORD] !== NULL) {
			$this->identity->password = $this->passwords->hash($values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_PASSWORD]);
		}

		if (
			$this->user->isAllowed('user', 'edit')
			&&
			isset($values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_ADMINISTRATOR])
		) {
			$this->identity->administrator = (bool) $values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_ADMINISTRATOR];
		}

		$this->usersRepository->persistAndFlush($this->identity);

		if ($this->user->isAllowed('user', 'edit')) {
			$this->getPresenter()->redirect(':DashBoard:User:');
		} else {
			$this->getPresenter()->redirect(':DashBoard:HomePage:');
		}
	}

}
