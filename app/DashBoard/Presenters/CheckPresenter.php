<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

final class CheckPresenter extends BasePresenter
{

	private \Pd\Monitoring\DashBoard\Controls\LogView\Factory $logViewFactory;

	private \Pd\Monitoring\Check\Check $check;

	private \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory $addEditCheckControlFactory;

	private \Pd\Monitoring\Project\Project $project;

	private int $type;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\LogView\Factory $logViewFactory,
		\Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory $addEditCheckControlFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository
	)
	{
		parent::__construct();
		$this->logViewFactory = $logViewFactory;
		$this->addEditCheckControlFactory = $addEditCheckControlFactory;
		$this->projectsRepository = $projectsRepository;
	}


	public function actionDefault(\Pd\Monitoring\Check\Check $check): void
	{

	}


	/**
	 * @Acl(check, add)
	 */
	public function actionAdd(int $project, int $type): void
	{
		$this->project = $this->projectsRepository->getById($project);
		if ( ! $this->project) {
			$this->error();
		}
		$this->type = $type;
	}


	public function createComponentAdd(): \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Control
	{
		$control = $this->addEditCheckControlFactory->create($this->project, $this->type);

		return $control;
	}


	/**
	 * @Acl(check, edit)
	 */
	public function actionEdit(\Pd\Monitoring\Check\Check $check): void
	{
		$this->check = $check;
		$this->project = $this->check->project;
		$this->type = $this->check->type;
	}


	public function createComponentEdit(): \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Control
	{
		$control = $this->addEditCheckControlFactory->create($this->project, $this->type, $this->check);

		return $control;
	}


	/**
	 * @Acl(check, view)
	 */
	public function actionLogView(\Pd\Monitoring\Check\Check $check): void
	{
		$this->check = $check;
		if ( ! $this->check) {
			$this->error();
		}
	}


	public function renderLogView(): void
	{
		$this
			->getTemplate()
			->add('check', $this->check)
		;
	}


	protected function createComponentLogView(): \Nette\Application\UI\Control
	{
		return $this->logViewFactory->create($this->check);
	}

}
