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


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\Check\Check $check = NULL,
		ICheckControlProcessor $checkControlProcessor,
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		parent::__construct();
		$this->formFactory = $formFactory;
		$this->checksRepository = $checksRepository;
		$this->project = $project;
		$this->checkControlProcessor = $checkControlProcessor;
		$this->check = $check;
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);

		if ( ! $this->check) {
			$this->check = $this->checkControlProcessor->getCheck();
		} else {
			$this['form']->setDefaults($this->check->toArray());
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
		$form->addCHeckbox('onlyErrors', 'Hlásit pouze chyby');

		$this->checkControlProcessor->createForm($this->check, $form);

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data) {
			$this->processForm($form, $data);
		};

		return $form;
	}


	private function processForm(\Nette\Forms\Form $form, array $data): void
	{
		$this->check->project = $this->project;

		$this->check->name = $data['name'];
		$this->check->onlyErrors = $data['onlyErrors'];

		$this->checkControlProcessor->processEntity($this->check, $data);

		$this->checksRepository->persistAndFlush($this->check);

		$this->getPresenter()->redirect(':DashBoard:Project:#' . $this->check->getType(), $this->project->id);
	}

}
