<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\UserOnProject;

final class Factory
{

	private const CLASS_GREEN = 'bg-success';
	private const CLASS_RED = 'bg-danger';


	private \Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory;

	private \Pd\Monitoring\UserOnProject\UserOnProjectRepository $userOnProjectRepository;

	private \Pd\Monitoring\User\UsersRepository $usersRepository;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory,
		\Pd\Monitoring\UserOnProject\UserOnProjectRepository $userOnProjectRepository,
		\Pd\Monitoring\User\UsersRepository $usersRepository
	)
	{
		$this->dataGridFactory = $dataGridFactory;
		$this->userOnProjectRepository = $userOnProjectRepository;
		$this->usersRepository = $usersRepository;
	}


	public function create(\Pd\Monitoring\Project\Project $project): \Ublaboo\DataGrid\DataGrid
	{
		$dataGrid = $this->dataGridFactory->create();

		$dataGrid->setPrimaryKey('user');

		$dataSource = new \Ublaboo\DataGrid\DataSource\NextrasDataSource($this->userOnProjectRepository->findBy(['project' => $project]), 'user');

		$dataGrid->setDataSource($dataSource);

		$cb = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): string
		{
			return $userOnProject->user->gitHubName;
		};
		$dataGrid
			->addColumnText('user', 'Uživatel')
			->setRenderer($cb)
		;

		$callback = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): bool
		{
			return $userOnProject->view;
		};
		self::addBooleanColumn($dataGrid, 'view', 'Prohlížet', $callback);

		$callback = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): bool
		{
			return $userOnProject->edit;
		};
		self::addBooleanColumn($dataGrid, 'edit', 'Upravovat', $callback);

		$callback = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): bool
		{
			return $userOnProject->admin;
		};
		self::addBooleanColumn($dataGrid, 'admin', 'Mazat', $callback);

		$cb = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): int
		{
			return $userOnProject->user->id;
		};
		$alreadyUsers = \array_map($cb, $dataSource->getData());

		$inlineAdd = $dataGrid->addInlineAdd();
		$addCallback = function (\Nette\Forms\Container $container) use ($alreadyUsers): void
		{
			$users = $this->usersRepository->findBy(['id!=' => $alreadyUsers])->fetchPairs('id', 'gitHubName');
			$container
				->addSelect('user', '', $users)
				->setRequired()
			;
			$container->addCheckbox('view', 'Prohlížet');
			$container->addCheckbox('edit', 'Upravovat');
			$container->addCheckbox('admin', 'Mazat');
		};
		$inlineAdd->setPositionTop()->onControlAdd[] = $addCallback;

		$inlineAdd->onSubmit[] = function ($values) use ($project): void
		{
			$userOnProject = new \Pd\Monitoring\UserOnProject\UserOnProject();
			$userOnProject->user = $this->usersRepository->getById($values->user);
			$userOnProject->project = $project;
			$userOnProject->view = $values->view;
			$userOnProject->edit = $values->edit;
			$userOnProject->admin = $values->admin;
			$this->userOnProjectRepository->persistAndFlush($userOnProject);
		};

		$inlineEdit = $dataGrid->addInlineEdit();
		$editCallback = static function (\Nette\Forms\Container $container): void
		{
			$container->addCheckbox('view', 'Prohlížet');
			$container->addCheckbox('edit', 'Upravovat');
			$container->addCheckbox('admin', 'Mazat');
		};
		$inlineEdit->onControlAdd[] = $editCallback;

		$inlineEdit->onSetDefaults[] = static function (\Nette\Forms\Container $container, \Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): void {
			$container->setDefaults(['view' => $userOnProject->view, 'edit' => $userOnProject->edit, 'admin' => $userOnProject->admin]);
		};

		$inlineEdit->onSubmit[] = function ($user, $values) use ($project): void
		{
			$userOnProject = $this->userOnProjectRepository->getBy(['user' => $user, 'project' => $project]);
			$userOnProject->view = $values->view;
			$userOnProject->edit = $values->edit;
			$userOnProject->admin = $values->admin;
			$this->userOnProjectRepository->persistAndFlush($userOnProject);
		};

		return $dataGrid;
	}


	private static function addBooleanColumn(\Ublaboo\DataGrid\DataGrid $dataGrid, string $key, string $name, callable $callback): \Ublaboo\DataGrid\Column\Column
	{
		$cb = static function (\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject) use ($callback): string
		{
			return $callback($userOnProject) ? 'Ano' : 'Ne';
		};

		$column = $dataGrid
			->addColumnText($key, $name)
			->setFitContent(TRUE)
			->setRenderer($cb)
		;

		$dataGrid->addColumnCallback($column->getColumnName(), static function (\Ublaboo\DataGrid\Column\Column $column, \Pd\Monitoring\UserOnProject\UserOnProject $userOnProject) use ($callback): void
		{
			$td = $column->getElementPrototype('td');
			$classes = (array) $td->getAttribute('class');

			unset($classes[self::CLASS_GREEN]);
			unset($classes[self::CLASS_RED]);

			$class = $callback($userOnProject) ? self::CLASS_GREEN : self::CLASS_RED;

			$classes[$class] = TRUE;
			$td->setAttribute('class', $classes);
		});

		return $column;
	}

}
