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

		$createDataSourceCb = function () use ($project): \Ublaboo\DataGrid\DataSource\ArrayDataSource
		{
			$data = [];
			foreach ($this->userOnProjectRepository->findBy(['project' => $project]) as $userOnProject) {
				$data[$userOnProject->user->id] = ['id' => $userOnProject->user->id, 'user' => $userOnProject->user->gitHubName, 'view' => $userOnProject->view, 'edit' => $userOnProject->edit, 'admin' => $userOnProject->admin];
			}

			return new \Ublaboo\DataGrid\DataSource\ArrayDataSource($data);
		};

		$dataGrid->setDataSource($createDataSourceCb());

		$dataGrid->addColumnText('user', 'Uživatel');

		self::addBooleanColumn($dataGrid, 'view', 'Prohlížet');

		self::addBooleanColumn($dataGrid, 'edit', 'Upravovat');

		self::addBooleanColumn($dataGrid, 'admin', 'Mazat');

		$alreadyUsers = \array_keys($dataGrid->getDataSource()->getData());

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

		$inlineAdd->onSubmit[] = function ($values) use ($project, $createDataSourceCb, $dataGrid): void
		{
			if ( ! $values->view && ! $values->edit && ! $values->admin) {
				$values->view = TRUE;
			}

			$userOnProject = new \Pd\Monitoring\UserOnProject\UserOnProject();
			$userOnProject->user = $this->usersRepository->getById($values->user);
			$userOnProject->project = $project;
			$userOnProject->view = $values->view;
			$userOnProject->edit = $values->edit;
			$userOnProject->admin = $values->admin;
			$this->userOnProjectRepository->persistAndFlush($userOnProject);

			$dataGrid->setDataSource($createDataSourceCb());
		};

		$inlineEdit = $dataGrid->addInlineEdit();
		$editCallback = static function (\Nette\Forms\Container $container): void
		{
			$container->addCheckbox('view', 'Prohlížet');
			$container->addCheckbox('edit', 'Upravovat');
			$container->addCheckbox('admin', 'Mazat');
		};
		$inlineEdit->onControlAdd[] = $editCallback;

		$inlineEdit->onSetDefaults[] = static function (\Nette\Forms\Container $container, array $userOnProject): void
		{
			$container->setDefaults($userOnProject);
		};

		$inlineEdit->onSubmit[] = function ($user, $values) use ($project, $createDataSourceCb, $dataGrid): void
		{
			if ( ! $values->view && ! $values->edit && ! $values->admin) {
				$values->view = TRUE;
			}

			$userOnProject = $this->userOnProjectRepository->getBy(['user' => $user, 'project' => $project]);
			$userOnProject->view = $values->view;
			$userOnProject->edit = $values->edit;
			$userOnProject->admin = $values->admin;
			$this->userOnProjectRepository->persistAndFlush($userOnProject);

			$dataGrid->setDataSource($createDataSourceCb());
		};

		$cb = function (string $id) use ($project, $createDataSourceCb, $dataGrid): void
		{
			$userOnProject = $this->userOnProjectRepository->getBy(['user' => $id, 'project' => $project]);
			$this->userOnProjectRepository->removeAndFlush($userOnProject);

			$dataGrid->setDataSource($createDataSourceCb());
		};
		$confirmation = new \Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation('Opravdu?');
		$dataGrid->addActionCallback('delete', 'Odebrat', $cb)->setConfirmation($confirmation);

		return $dataGrid;
	}


	private static function addBooleanColumn(\Ublaboo\DataGrid\DataGrid $dataGrid, string $key, string $name): \Ublaboo\DataGrid\Column\Column
	{
		$column = $dataGrid
			->addColumnText($key, $name)
			->setFitContent(TRUE)
			->setReplacement([TRUE => 'Ano', FALSE => 'Ne'])
		;

		$dataGrid->addColumnCallback($column->getColumnName(), static function (\Ublaboo\DataGrid\Column\Column $column, array $userOnProject) use ($key): void
		{
			$td = $column->getElementPrototype('td');
			$classes = (array) $td->getAttribute('class');

			unset($classes[self::CLASS_GREEN]);
			unset($classes[self::CLASS_RED]);

			$class = $userOnProject[$key] ? self::CLASS_GREEN : self::CLASS_RED;

			$classes[$class] = TRUE;
			$td->setAttribute('class', $classes);
		});

		return $column;
	}

}
