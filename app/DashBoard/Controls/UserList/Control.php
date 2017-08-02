<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\UserList;

class Control extends \Nette\Application\UI\Control
{

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


	public function render()
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

		$grid->setDataSource($this->usersRepository->findAll());

		return $grid;
	}


	public function handleEdit(int $id)
	{
		$this->getPresenter()->redirect(':DashBoard:User:edit', $id);
	}

}
