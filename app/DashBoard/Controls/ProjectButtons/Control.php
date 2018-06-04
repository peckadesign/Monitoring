<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectButtons;

final class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\Maintenance\IFactory
	 */
	private $maintenanceControlFactory;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\DashBoard\Controls\Maintenance\IFactory $maintenanceControlFactory
	)
	{
		$this->project = $project;
		$this->maintenanceControlFactory = $maintenanceControlFactory;
	}


	public function render(): void
	{
		$this
			->getTemplate()
			->setFile(__DIR__ . '/Control.latte')
			->add('project', $this->project)
			->add('checks', $this->project->checks)
			->render()
		;
	}


	protected function createComponentMaintenance(): \Pd\Monitoring\DashBoard\Controls\Maintenance\Control
	{
		$control = $this->maintenanceControlFactory->create($this->project);

		$presenter = $this->getPresenter();
		if ($presenter->isAjax()) {
			$handler = new class($presenter) implements \Pd\Monitoring\DashBoard\Controls\Maintenance\IOnToggle
			{

				/**
				 * @var \Pd\Monitoring\DashBoard\Presenters\ProjectPresenter
				 */
				private $presenter;


				public function __construct(
					\Pd\Monitoring\DashBoard\Presenters\ProjectPresenter $presenter
				) {
					$this->presenter = $presenter;
				}


				public function process(\Pd\Monitoring\DashBoard\Controls\Maintenance\Control $control)
				{
					$this->presenter->redrawControl('title');
					$this->presenter->redrawControl('heading');
				}

			};
			$control->addOnToggle($handler);
		}

		return $control;
	}

}
