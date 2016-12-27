<?php

namespace Pd\Monitoring\DashBoard\Presenters;

class ProjectPresenter extends BasePresenter
{

	use \Pd\Monitoring\DashBoard\Controls\Refresh\TFactory;

	/**
	 * @var \Pd\Monitoring\DashBoard\Forms\Factory
	 */
	private $formFactory;

	/**
	 * @var \Pd\Monitoring\Project\ProjectsRepository
	 */
	private $projectsRepository;

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\Check\IFactory
	 */
	private $checkControlFactory;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var int
	 */
	private $type;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory
	 */
	private $addEditCheckControlFactory;

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	private $check;


	public function __construct(
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\DashBoard\Controls\Check\IFactory $checkControlFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory $addEditCheckControlFactory
	) {
		$this->formFactory = $formFactory;
		$this->projectsRepository = $projectsRepository;
		$this->checkControlFactory = $checkControlFactory;
		$this->checksRepository = $checksRepository;
		$this->addEditCheckControlFactory = $addEditCheckControlFactory;
	}


	/**
	 * @Acl(project, add)
	 */
	public function actionAdd()
	{
	}


	/**
	 * @Acl(project, add)
	 */
	public function actionEdit($id)
	{
		$this->project = $this->projectsRepository->getById($id);

		$this['addEditForm']->setDefaults($this->project->toArray());
	}


	protected function createComponentAddEditForm(): \Nette\Application\UI\Form
	{
		$form = $this->formFactory->create();

		$form->addText('name', 'Název projektu');
		$form->addText('url', 'URL projektu');

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data) {
			$this->processAddEditForm($form, $data);
		};

		return $form;
	}


	private function processAddEditForm(\Nette\Forms\Form $form, array $data)
	{
		if ($this->project) {
			$project = $this->project;
		} else {
			$project = new \Pd\Monitoring\Project\Project();
		}
		$project->name = $data['name'];
		$project->url = $data['url'];

		$project = $this->projectsRepository->persistAndFlush($project);

		$this->redirect(':DashBoard:Project:', $project->id);
	}


	public function actionDefault(int $id)
	{
		$this->project = $this->projectsRepository->getById($id);
	}


	public function renderDefault()
	{
		$this->template->project = $this->project;

		$this->template->checks = [
			new \Pd\Monitoring\Check\AliveCheck(),
			new \Pd\Monitoring\Check\TermCheck(),
			new \Pd\Monitoring\Check\DnsCheck(),
		];
	}


	protected function createComponentCheck()
	{
		$cb = function ($id) {
			$check = $this->checksRepository->getById($id);

			return $this->checkControlFactory->create($check);
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}


	/**
	 * @Acl(check, add)
	 */
	public function actionAddCheck(int $id, int $type)
	{
		$this->project = $this->projectsRepository->getById($id);
		$this->type = $type;
	}


	public function createComponentAddCheck(): \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Control
	{
		$control = $this->addEditCheckControlFactory->create($this->project, $this->type);

		return $control;
	}


	/**
	 * @Acl(check, edit)
	 */
	public function actionEditCheck(int $id, int $checkId)
	{
		$this->project = $this->projectsRepository->getById($id);
		$this->check = $this->checksRepository->getById($checkId);
		$this->type = $this->check->type;
	}


	public function createComponentEditCheck(): \Pd\Monitoring\DashBoard\Controls\AddEditCheck\Control
	{
		$control = $this->addEditCheckControlFactory->create($this->project, $this->type, $this->check);

		return $control;
	}


	/**
	 * @Acl(project, delete)
	 */
	public function handleDelete()
	{
		try {
			$this->projectsRepository->removeAndFlush($this->project);
			$this->redirect(':DashBoard:HomePage:');
		} catch (\Nextras\Orm\InvalidStateException $e) {
		}
	}

}
