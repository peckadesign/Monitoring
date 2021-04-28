<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

class ProjectPresenter extends BasePresenter
{

	use \Pd\Monitoring\DashBoard\Controls\Refresh\TFactory;


	private \Pd\Monitoring\DashBoard\Forms\Factory $formFactory;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;

	private ?\Pd\Monitoring\Project\Project $project = NULL;

	private \Pd\Monitoring\DashBoard\Controls\ProjectChecks\IFactory $projectChecksControlFactory;

	private \Pd\Monitoring\DashBoard\Controls\ProjectButtons\IFactory $projectButtonsFactory;

	private int $type;

	private \Pd\Monitoring\DashBoard\Controls\SubProjects\IFactory $subProjectsControlFactory;

	private \Pd\Monitoring\DashBoard\Controls\UserOnProject\Factory $userOnProjectGridFactory;


	public function __construct(
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\DashBoard\Controls\ProjectChecks\IFactory $projectChecksControlFactory,
		\Pd\Monitoring\DashBoard\Controls\ProjectButtons\IFactory $projectButtonsFactory,
		\Pd\Monitoring\DashBoard\Controls\SubProjects\IFactory $subProjectsControlFactory,
		\Pd\Monitoring\DashBoard\Controls\UserOnProject\Factory $userOnProjectGridFactory
	)
	{
		parent::__construct();
		$this->formFactory = $formFactory;
		$this->projectsRepository = $projectsRepository;
		$this->projectChecksControlFactory = $projectChecksControlFactory;
		$this->projectButtonsFactory = $projectButtonsFactory;
		$this->subProjectsControlFactory = $subProjectsControlFactory;
		$this->userOnProjectGridFactory = $userOnProjectGridFactory;
	}


	/**
	 * @Acl(project, add)
	 */
	public function actionAdd(?int $parent = NULL): void
	{
		$this['addEditForm']->setDefaults(['parent' => $parent]);
	}


	public function actionEdit(\Pd\Monitoring\Project\Project $project): void
	{
		if ( ! $this->user->isAllowed($project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		$this->project = $project;

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

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data)
		{
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
		if (isset($data['parent'])) {
			$project->parent = $this->projectsRepository->getById((int) $data['parent']);
		}
		$project->reference = $data['reference'];

		$project = $this->projectsRepository->persistAndFlush($project);

		$this->redirect(':DashBoard:Project:', $project);
	}


	public function actionDefault(\Pd\Monitoring\Project\Project $project, int $type = \Pd\Monitoring\Check\ICheck::TYPE_ALIVE): void
	{
		if ( ! $this->user->isAllowed($project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		$this->project = $project;
		$this->type = $type;
	}


	public function renderDefault(): void
	{
		$this
			->getTemplate()
			->add('project', $this->project)
			->add('type', $this->type)
		;
	}


	protected function createComponentProjectChecks(): \Pd\Monitoring\DashBoard\Controls\ProjectChecks\Control
	{
		return $this->projectChecksControlFactory->create($this->project, $this->type);
	}


	public function actionDelete(\Pd\Monitoring\Project\Project $project): void
	{
		if ( ! $this->user->isAllowed($project, \Pd\Monitoring\User\AclFactory::PRIVILEGE_DELETE)) {
			throw new \Nette\Application\ForbiddenRequestException();
		}

		$parent = $project->parent;

		try {
			$this->projectsRepository->removeAndFlush($project);
			$this->flashMessage('Projekt byl smazán', self::FLASH_MESSAGE_SUCCESS);
		} catch (\Nextras\Orm\InvalidStateException $e) {
			$this->flashMessage('Nepodařilo se smazat projekt', self::FLASH_MESSAGE_ERROR);
		}

		if ($parent) {
			$this->redirect(':DashBoard:Project:', $parent);
		} else {
			$this->redirect(':DashBoard:HomePage:');
		}
	}


	protected function createComponentProjectButtons(): \Nette\Application\UI\Multiplier
	{
		$cb = function (string $id): \Pd\Monitoring\DashBoard\Controls\ProjectButtons\Control
		{
			return $this->projectButtonsFactory->create($this->projectsRepository->getById((int) $id));
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}


	protected function createComponentSubProjects(): \Pd\Monitoring\DashBoard\Controls\SubProjects\Control
	{
		return $this->subProjectsControlFactory->create($this->project);
	}


	protected function createComponentUserOnProjectGrid(): \Ublaboo\DataGrid\DataGrid
	{
		return $this->userOnProjectGridFactory->create($this->project);
	}

}
