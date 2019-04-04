<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecks;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\Check\IFactory
	 */
	private $checkControlFactory;

	/**
	 * @var iterable
	 */
	private $checks;

	/**
	 * @var \Pd\Monitoring\User\User
	 */
	private $user;

	/**
	 * @var \Nextras\Orm\Relationships\OneHasMany
	 */
	private $userCheckNotifications;

	/**
	 * @var int
	 */
	private $type;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\IFactory
	 */
	private $projectChecksTabsControlFactory;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		int $type,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\Check\IFactory $checkControlFactory,
		\Pd\Monitoring\User\User $user,
		\Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\IFactory $projectChecksTabsControlFactory
	) {
		parent::__construct();
		$this->project = $project;
		$this->checksRepository = $checksRepository;
		$this->checkControlFactory = $checkControlFactory;
		$this->user = $user;
		$this->type = $type;
		$this->projectChecksTabsControlFactory = $projectChecksTabsControlFactory;
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);

		$conditions = [
			'project' => $this->project->id,
			'type' => $this->type,
		];
		$this->checks = $this->checksRepository->findBy($conditions);

		$this->userCheckNotifications = $this->user->userCheckNotifications;
	}


	public function render(): void
	{
		$checks = [];

		/** @var \Pd\Monitoring\Check\Check $check */
		foreach ($this->checks as $check) {
			$checks[$check->id] = $check;
		}

		$this
			->getTemplate()
			->setFile(__DIR__ . '/Control.latte')
			->add('checks', $checks)
			->add('project', $this->project)
			->add('type', $this->type)
			->render()
		;
	}


	protected function createComponentCheck(): \Nette\Application\UI\Multiplier
	{
		$cb = function ($id) {
			$check = $this->checksRepository->getById($id);

			return $this->checkControlFactory->create($check, $this->userCheckNotifications->has([$this->user->id, $check->id]));
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}


	protected function createComponentTabs(): \Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\Control
	{
		return $this->projectChecksTabsControlFactory->create($this->project, $this->type);
	}

}
