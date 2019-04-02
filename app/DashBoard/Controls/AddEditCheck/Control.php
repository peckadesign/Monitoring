<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Forms\Factory
	 */
	private $formFactory;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	protected $checksRepository;

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	protected $project;

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	protected $check;

	/**
	 * @var ICheckControlProcessor
	 */
	protected $checkControlProcessor;

	/**
	 * @var \Pd\Monitoring\Project\ProjectsRepository
	 */
	private $projectsRepository;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\Check\Check $check = NULL,
		ICheckControlProcessor $checkControlProcessor,
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository
	) {
		parent::__construct();
		$this->formFactory = $formFactory;
		$this->checksRepository = $checksRepository;
		$this->project = $project;
		$this->checkControlProcessor = $checkControlProcessor;
		$this->check = $check;
		$this->projectsRepository = $projectsRepository;
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);

		if ( ! $this->check) {
			$this->check = $this->checkControlProcessor->getCheck();
			$this['form']->setDefaults(['project' => $this->project->id]);
		} else {
			$this['form']->setDefaults($this->check->toArray(\Nextras\Orm\Entity\ToArrayConverter::RELATIONSHIP_AS_ID));
		}
	}


	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	protected function createComponentForm(): \Nette\Forms\Form
	{
		$form = $this->formFactory->create();

		$form->addGroup('Obecné informace');
		$form->addText('name', 'Vlastní název');
		$form->addCheckbox('onlyErrors', 'Hlásit pouze chyby');
		$form->addCheckbox('reference', 'Referenční kontrola pro projekt');

		$form
			->addText('pausedFrom', 'Pozastavení notifikace od')
			->setAttribute('placeholder', 'hh:mm')
		;
		$form
			->addText('pausedTo', 'Pozastavení notifikace do')
			->setAttribute('placeholder', 'hh:mm')
		;

		$projects = $this->projectsRepository->findAll()->orderBy('name')->fetchPairs('id', 'name');
		$form->addSelect('project', 'Projekt', $projects);

		$form->addGroup($this->check->getTitle());

		$this->checkControlProcessor->createForm($this->check, $form);

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data) {
			$this->processForm($form, $data);
		};

		return $form;
	}


	private function processForm(\Nette\Forms\Form $form, array $data): void
	{
		$this->check->project = $this->projectsRepository->getById($data['project']);

		$this->check->name = $data['name'];
		$this->check->onlyErrors = $data['onlyErrors'];
		$this->check->reference = $data['reference'];
		$this->check->pausedFrom = $data['pausedFrom'];
		$this->check->pausedTo = $data['pausedTo'];
		$this->check->url = $data['url'];

		$this->checkControlProcessor->processEntity($this->check, $data);

		$this->checksRepository->persistAndFlush($this->check);

		$this->getPresenter()->redirect(':DashBoard:Project:#' . $this->project->id . '-' . $this->check->getType(), $this->project->id);
	}

}
