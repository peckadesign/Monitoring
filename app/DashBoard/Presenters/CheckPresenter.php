<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

final class CheckPresenter extends BasePresenter
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\LogView\Factory
	 */
	private $logViewFactory;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	private $check;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory
	 */
	private $addEditCheckControlFactory;

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var int
	 */
	private $type;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\LogView\Factory $logViewFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory $addEditCheckControlFactory
	) {
		parent::__construct();
		$this->logViewFactory = $logViewFactory;
		$this->checksRepository = $checksRepository;
		$this->addEditCheckControlFactory = $addEditCheckControlFactory;
	}


	/**
	 * @Acl(check, add)
	 */
	public function actionAdd(\Pd\Monitoring\Project\Project $project, int $type): void
	{
		$this->project = $project;
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
	public function actionEdit(\Pd\Monitoring\Project\Project $project, int $checkId): void
	{
		$this->project = $project;
		$this->check = $this->checksRepository->getById($checkId);
		$this->type = $this->check->type;
	}


	public function createComponentEditCheck(): \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Control
	{
		$control = $this->addEditCheckControlFactory->create($this->project, $this->type, $this->check);

		return $control;
	}


	/**
	 * @Acl(check, edit)
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
