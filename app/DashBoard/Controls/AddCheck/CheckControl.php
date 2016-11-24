<?php

namespace Pd\Monitoring\DashBoard\Controls\AddCheck;

abstract class CheckControl extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Forms\Factory
	 */
	private $formFactory;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var int
	 */
	private $type;

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	protected $check;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		int $type,
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		parent::__construct();
		$this->formFactory = $formFactory;
		$this->checksRepository = $checksRepository;
		$this->project = $project;
		$this->type = $type;
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);

		$this->check = $this->getCheck();
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	protected function createComponentAddForm()
	{
		$form = $this->formFactory->create();

		$this->createAddForm($form);

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = function (\Nette\Forms\Form $form, array $data) {
			$this->processAddForm($form, $data);
		};

		return $form;
	}


	private function processAddForm(\Nette\Forms\Form $form, array $data)
	{
		$this->check->status = \Pd\Monitoring\Check\ICheck::STATUS_ERROR;
		$this->check->project = $this->project;

		$this->processNewEntity($data);

		$this->checksRepository->persistAndFlush($this->check);

		$this->getPresenter()->redirect(':DashBoard:Project:', $this->project->id);
	}


	abstract protected function processNewEntity(array $data);

	abstract protected function getCheck() : \Pd\Monitoring\Check\Check;

	abstract protected function createAddForm(\Nette\Application\UI\Form $form);

}
