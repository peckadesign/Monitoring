<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs;

final class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var int
	 */
	private $type;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		int $type
	) {
		parent::__construct();
		$this->project = $project;
		$this->type = $type;
	}


	protected function createTemplate()
	{
		$template = parent::createTemplate();

		$template->addFilter('tabColor', function (Tab $tab) {
			switch ($tab->getStatus()) {
				case \Pd\Monitoring\Check\ICheck::STATUS_OK:
					return 'badge-success';
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					return 'badge-warning';
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					return 'badge-danger';
				default:
					return 'badge-danger';
			}
		});

		return $template;
	}


	public function render(): void
	{
		/** @var array|Tab[] $tabs */
		$tabs = [];
		/** @var \Pd\Monitoring\Check\Check $check */
		foreach ($this->project->checks as $check) {
			if ( ! isset($tabs[$check->getType()]) || $check->status > $tabs[$check->getType()]->getStatus()) {
				$tabs[$check->getType()] = new Tab($check->getTitle(), $check->status);
			}
			$tabs[$check->getType()]->incrementCount();
		}

		$this
			->getTemplate()
			->setFile(__DIR__ . '/Control.latte')
			->add('tabs', $tabs)
			->add('project', $this->project)
			->add('type', $this->type)
			->render()
		;
	}

}
