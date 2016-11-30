<?php

namespace Pd\Monitoring\DashBoard\Controls\Project;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;


	public function __construct(
		\Pd\Monitoring\Project\Project $project
	) {
		parent::__construct();
		$this->project = $project;
	}


	protected function createTemplate()
	{
		$template = parent::createTemplate();

		$template->addFilter('check', function (int $value) {
			switch ($value) {
				case \Pd\Monitoring\Check\ICheck::STATUS_OK:
					return 'success';
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					return 'warning';
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					return 'danger';
				default:
					return 'danger';
			}
		});

		return $template;
	}


	public function render()
	{
		$this->template->project = $this->project;

		$checks = [
			\Pd\Monitoring\Check\ICheck::STATUS_OK => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ALERT => [],
			\Pd\Monitoring\Check\ICheck::STATUS_ERROR => [],
		];
		foreach ($this->project->checks as $check) {
			if ($check->paused) {
				continue;
			}
			if ( ! isset($checks[$check->status])) {
				$checks[$check->status] = [];
			}
			$checks[$check->status][$check->id] = $check;
		}
		$total = count($this->project->checks);
		$percents = [];
		if ($total) {
			foreach ($checks as $status => $checksForStatus) {
				$percents[$status] = (count($checksForStatus) * 100) / $total;
			}
		}
		$this->template->checks = $checks;
		$this->template->percents = $percents;

		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}

}
