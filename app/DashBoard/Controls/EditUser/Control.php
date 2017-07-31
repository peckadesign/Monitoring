<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\EditUser;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Forms\UserEditFormFactory
	 */
	private $userEditFormFactory;

	/**
	 * @var \Pd\Monitoring\User\User
	 */
	private $identity;

	/**
	 * @var \Pd\Monitoring\User\UsersRepository
	 */
	private $usersRepository;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(
		\Pd\Monitoring\User\User $identity,
		\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory $userEditFormFactory,
		\Pd\Monitoring\User\UsersRepository $usersRepository,
		\Nette\Security\User $user
	) {
		$this->identity = $identity;
		$this->userEditFormFactory = $userEditFormFactory;
		$this->usersRepository = $usersRepository;
		$this->user = $user;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);

		$this['form']->setDefaults($this->identity->toArray());
	}


	protected function createComponentForm(): \Nette\Application\UI\Form
	{
		$form = $this->userEditFormFactory->create();

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $values) {
			$this->processEditForm($form, $values);
		};

		return $form;
	}


	protected function processEditForm(\Nette\Forms\Form $form, array $values)
	{
		$this->identity->gitHubName = $values[\Pd\Monitoring\DashBoard\Forms\UserEditFormFactory::FIELD_GIT_HUB_NAME];

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
