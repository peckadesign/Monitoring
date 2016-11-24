<?php

namespace Pd\Monitoring\DashBoard\Presenters;

class ProjectPresenter extends BasePresenter
{

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
	 * @var \Pd\Monitoring\DashBoard\Controls\AddCheck\Factory
	 */
	private $addCheckControlFactory;

	/**
	 * @var int
	 */
	private $type;


	public function __construct(
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\DashBoard\Controls\Check\IFactory $checkControlFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\AddCheck\Factory $addCheckControlFactory
	) {
		$this->formFactory = $formFactory;
		$this->projectsRepository = $projectsRepository;
		$this->checkControlFactory = $checkControlFactory;
		$this->checksRepository = $checksRepository;
		$this->addCheckControlFactory = $addCheckControlFactory;
	}


	public function actionAdd()
	{
	}


	protected function createComponentAddForm() : \Nette\Application\UI\Form
	{
		$form = $this->formFactory->create();

		$form->addText('name', 'Název projektu');
		$form->addText('url', 'URL projektu');

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data) {
			$this->processAddForm($form, $data);
		};

		return $form;
	}


	private function processAddForm(\Nette\Forms\Form $form, array $data)
	{
		$project = new \Pd\Monitoring\Project\Project();
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


	public function actionAddCheck(int $id, int $type)
	{
		$this->project = $this->projectsRepository->getById($id);
		$this->type = $type;
	}


	public function createComponentAddCheck() : \Pd\Monitoring\DashBoard\Controls\AddCheck\CheckControl
	{
		$control = $this->addCheckControlFactory->create($this->project, $this->type);

		return $control;
	}


	public function handleDelete()
	{
		$this->projectsRepository->removeAndFlush($this->project);
		$this->redirect(':DashBoard:HomePage:');
	}

}
