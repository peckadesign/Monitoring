<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

class UserPresenter extends BasePresenter
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\EditUser\IFactory
	 */
	private $editUserControlFactory;

	/**
	 * @var \Pd\Monitoring\User\User
	 */
	private $editedUser;

	/**
	 * @var \Pd\Monitoring\User\UsersRepository
	 */
	private $usersRepository;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\UserList\IFactory
	 */
	private $userListControlFactory;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\EditUser\IFactory $editUserControlFactory,
		\Pd\Monitoring\User\UsersRepository $usersRepository,
		\Pd\Monitoring\DashBoard\Controls\UserList\IFactory $userListControlFactory
	) {
		parent::__construct();
		$this->editUserControlFactory = $editUserControlFactory;
		$this->usersRepository = $usersRepository;
		$this->userListControlFactory = $userListControlFactory;
	}


	public function actionDefault(): void
	{
		if ( ! $this->user->isAllowed('user', 'edit')) {
			throw new \Nette\Application\ForbiddenRequestException();
		}
	}


	public function actionEdit(int $id): void
	{
		if (
			! $this->user->isAllowed('user', 'edit')
			&&
			$this->user->id !== $id
		) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		$this->editedUser = $this->usersRepository->getById($id);
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
