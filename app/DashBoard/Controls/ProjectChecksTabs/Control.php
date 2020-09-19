<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs;

final class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Project\Project $project;

	private int $type;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		int $type
	)
	{
		$this->project = $project;
		$this->type = $type;
	}


	protected function createTemplate(): \Nette\Application\UI\ITemplate
	{
		$template = parent::createTemplate();

		$template->addFilter('tabColor', static function (Tab $tab)
		{
			switch ($tab->getStatus()) {
				case \Pd\Monitoring\Check\ICheck::STATUS_OK:
					return 'bg-success';
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					return 'bg-warning';
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					return 'bg-danger';
				default:
					return 'bg-danger';
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
