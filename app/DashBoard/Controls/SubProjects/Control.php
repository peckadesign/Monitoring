<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SubProjects;

final class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Project\Project $project;

	private \Nette\Security\User $user;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Nette\Security\User $user
	)
	{
		$this->project = $project;
		$this->user = $user;
	}


	public function render(): void
	{
		if ( ! $this->project->parent) {
			return;
		}

		$tabs = [];
		foreach ($this->project->parent->subProjects as $subProject) {
			if ( ! $this->user->isAllowed($subProject->getResourceId(), \Pd\Monitoring\User\AclFactory::PRIVILEGE_VIEW)) {
				continue;
			}
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
