<?php declare(strict_types = 1);

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

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\ProjectChecks\IFactory
	 */
	private $projectChecksControlFactory;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\ProjectButtons\IFactory
	 */
	private $projectButtonsFactory;


	public function __construct(
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\AddEditCheck\Factory $addEditCheckControlFactory,
		\Pd\Monitoring\DashBoard\Controls\ProjectChecks\IFactory $projectChecksControlFactory,
		\Pd\Monitoring\DashBoard\Controls\ProjectButtons\IFactory $projectButtonsFactory
	) {
		parent::__construct();
		$this->formFactory = $formFactory;
		$this->projectsRepository = $projectsRepository;
		$this->checksRepository = $checksRepository;
		$this->addEditCheckControlFactory = $addEditCheckControlFactory;
		$this->projectChecksControlFactory = $projectChecksControlFactory;
		$this->projectButtonsFactory = $projectButtonsFactory;
	}


	/**
	 * @Acl(project, add)
	 */
	public function actionAdd(?int $parent = NULL): void
	{
		$this['addEditForm']->setDefaults(['parent' => $parent]);
	}


	/**
	 * @Acl(project, add)
	 */
	public function actionEdit(int $id): void
	{
		/** @var \Pd\Monitoring\Project\Project project */
		$this->project = $this->projectsRepository->getById($id);

		$this['addEditForm']->setDefaults($this->project->toArray(\Nextras\Orm\Entity\ToArrayConverter::RELATIONSHIP_AS_ID));
	}


	protected function createComponentAddEditForm(): \Nette\Application\UI\Form
	{
		$form = $this->formFactory->create();

		$form->addText('name', 'Název projektu');
		$form->addText('url', 'URL projektu');
		$form
			->addText('pausedFrom', 'Pozastavení notifikace od')
			->setAttribute('placeholder', 'hh:mm')
		;
		$form
			->addText('pausedTo', 'Pozastavení notifikace do')
			->setAttribute('placeholder', 'hh:mm')
		;
		$form
			->addCheckbox('notifications', 'Povolené globální notifikace')
			->setDefaultValue(TRUE)
		;

		$projects = $this->projectsRepository->findParentAbleProjects($this->project)->fetchPairs('id', 'name');

		$form
			->addSelect('parent', 'Zastřešující projekt', $projects, 1)
			->setPrompt('Vyberte')
			->setDisabled($this->project && \count($this->project->subProjects))
		;

		$form->addCheckbox('reference', 'Referenční projekt');


		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data) {
			$this->processAddEditForm($form, $data);
		};

		return $form;
	}


	private function processAddEditForm(\Nette\Forms\Form $form, array $data): void
	{
		if ($this->project) {
			$project = $this->project;
		} else {
			$project = new \Pd\Monitoring\Project\Project();
		}
		$project->name = $data['name'];
		$project->url = $data['url'];
		$project->pausedFrom = $data['pausedFrom'];
		$project->pausedTo = $data['pausedTo'];
		$project->notifications = $data['notifications'];
		$project->parent = $this->projectsRepository->getById((int) $data['parent']);
		$project->reference = $data['reference'];

		$project = $this->projectsRepository->persistAndFlush($project);

		$this->redirect(':DashBoard:Project:', $project->id);
	}


	public function actionDefault(int $id): void
	{
		$this->project = $this->projectsRepository->getById($id);

		if ($this->project->parent) {
			$this->redirect('default#project-' . $this->project->id, $this->project->parent->id);
		}
	}


	public function renderDefault(): void
	{
		$this
			->getTemplate()
			->add('project', $this->project)
			->add('subProjects', $this->project->subProjects)
		;
	}


	protected function createComponentProjectChecks(): \Nette\Application\UI\Multiplier
	{
		$cb = function (string $id) {
			return $this->projectChecksControlFactory->create($this->projectsRepository->getById((int) $id));
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}


	/**
	 * @Acl(check, add)
	 */
	public function actionAddCheck(int $id, int $type): void
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
	public function actionEditCheck(int $id, int $checkId): void
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
	public function actionDelete(int $id): void
	{
		$this->project = $this->projectsRepository->getById($id);
		$parent = $this->project->parent;

		try {
			$this->projectsRepository->removeAndFlush($this->project);
			$this->flashMessage('Projekt byl smazán', self::FLASH_MESSAGE_SUCCESS);
		} catch (\Nextras\Orm\InvalidStateException $e) {
			$this->flashMessage('Nepodařilo se smazat projekt', self::FLASH_MESSAGE_ERROR);
		}

		if ($parent) {
			$this->redirect(':DashBoard:Project:', $parent->id);
		} else {
			$this->redirect(':DashBoard:HomePage:');
		}
	}


	protected function createComponentProjectButtons(): \Nette\Application\UI\Multiplier
	{
		$cb = function (string $id): \Pd\Monitoring\DashBoard\Controls\ProjectButtons\Control {
			return $this->projectButtonsFactory->create($this->projectsRepository->getById((int) $id));
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}

}
