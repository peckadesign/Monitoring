<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SubProjects;

final class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\IFactory
	 */
	private $projectChecksTabsControlFactory;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\IFactory $projectChecksTabsControlFactory
	) {
		$this->project = $project;
		$this->projectChecksTabsControlFactory = $projectChecksTabsControlFactory;
	}


	public function render(): void
	{
		if ($this->project->parent) {
			$projects = $this->project->parent->subProjects;
		} else {
			$projects = [$this->project];
		}

		$tabs = [];
		foreach ($projects as $subProject) {
			$tabs[] = new Tab($subProject, $subProject === $this->project);
		}

		$this
			->getTemplate()
			->setFile(__DIR__ . '/Control.latte')
			->add('tabs', $tabs)
			->render()
		;
	}


	protected function createComponentTabs(): \Nette\Application\UI\Multiplier
	{
		$cb = function (string $id): \Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs\Control {
			return $this->projectChecksTabsControlFactory->create($this->project->parent ? $this->project->parent->subProjects->get()->getById((int) $id) : $this->project, 1);
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}

}
