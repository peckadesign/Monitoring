<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\UserList;

class Control extends \Nette\Application\UI\Control
{

	use \Pd\Monitoring\DashBoard\TSecuredComponent;

	/**
	 * @var \Pd\Monitoring\User\UsersRepository
	 */
	private $usersRepository;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\DataGridFactory
	 */
	private $dataGridFactory;


	public function __construct(
		\Pd\Monitoring\User\UsersRepository $usersRepository,
		\Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory
	) {
		$this->usersRepository = $usersRepository;
		$this->dataGridFactory = $dataGridFactory;
	}


	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	protected function createComponentListGrid(): \Nette\Application\UI\Control
	{
		$grid = $this->dataGridFactory->create();

		$grid
			->addColumnText('gitHubName', 'Jméno')
			->setFilterText(['git_hub_name'])
		;

		$administratorReplacement = [
			1 => 'Ano',
			0 => 'Ne',
		];
		$administratorFilter = [
			'' => 'Všechno',
		] + $administratorReplacement;
		$grid
			->addColumnText('administrator', 'Administrátor')
			->setReplacement($administratorReplacement)
			->setFilterSelect($administratorFilter)
		;

		$grid
			->addAction('edit', 'Upravit')
			->setClass('btn btn-warning')
		;

		$grid
			->addAction('delete', 'Smazat')
			->setClass('btn btn-danger')
			->setDataAttribute('confirm', '')
		;


		$grid->setDataSource($this->usersRepository->findAll());

		return $grid;
	}


	public function handleEdit(int $id): void
	{
		$user = $this->usersRepository->getById($id);
		$this->getPresenter()->redirect(':DashBoard:User:edit', $user);
	}


	/**
	 * @Acl(user, delete)
	 * @throws \Nette\Application\AbortException
	 */
	public function handleDelete(int $id): void
	{
		$this->usersRepository->removeAndFlush($this->usersRepository->getById($id));
		$this->getPresenter()->flashMessage('Uživatel byl smazán', \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		$this->redirect('this');
	}

}
