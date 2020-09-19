<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SubProjects;

final class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Project\Project $project;


	public function __construct(\Pd\Monitoring\Project\Project $project)
	{
		$this->project = $project;
	}


	public function render(): void
	{
		if ( ! $this->project->parent) {
			return;
		}

		$tabs = [];
		foreach ($this->project->parent->subProjects as $subProject) {
			$tabs[] = new Tab($subProject, $subProject === $this->project);
		}

		$this
			->getTemplate()
			->setFile(__DIR__ . '/Control.latte')
			->add('tabs', $tabs)
			->render()
		;
	}

}
