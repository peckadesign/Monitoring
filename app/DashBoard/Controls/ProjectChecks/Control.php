<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecks;

class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Project\Project $project;

	private \Pd\Monitoring\Check\ChecksRepository $checksRepository;

	private \Pd\Monitoring\DashBoard\Controls\Check\IFactory $checkControlFactory;

	/**
	 * @var iterable<\Pd\Monitoring\Check\Check>
	 */
	private iterable $checks;

	private \Pd\Monitoring\User\User $user;

	private \Nextras\Orm\Relationships\OneHasMany $userCheckNotifications;

	private int $type;

	private \Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\IFactory $projectChecksTabsControlFactory;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		int $type,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\Check\IFactory $checkControlFactory,
		\Pd\Monitoring\User\User $user,
		\Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\IFactory $projectChecksTabsControlFactory,
		\Pd\Monitoring\UserOnProject\UserOnProjectRepository $userOnProjectRepository
	)
	{
		$this->project = $project;
		$this->checksRepository = $checksRepository;
		$this->checkControlFactory = $checkControlFactory;
		$this->user = $user;
		$this->type = $type;
		$this->projectChecksTabsControlFactory = $projectChecksTabsControlFactory;

		$this->onAnchor[] = function (\Nette\Application\UI\Control $control) use ($userOnProjectRepository): void
		{
			$conditions = [
				'type' => $this->type,
			];

			if ( ! $this->user->administrator) {
				$projectsIds = \array_map(static fn(\Pd\Monitoring\UserOnProject\UserOnProject $userOnProject): int => $userOnProject->project->id, \iterator_to_array($userOnProjectRepository->findBy(['user' => $this->user->getId()])->getIterator()));
				$conditions['project'] = \array_intersect($projectsIds, [$this->project->id]);
			} else {
				$conditions['project'] = $this->project->id;
			}

			$this->checks = $this->checksRepository->findBy($conditions);

			$this->userCheckNotifications = $this->user->userCheckNotifications;
		};
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
		$cb = function ($id)
		{
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
