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
					return 'OK';
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					return 'ALERT';
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					return 'ERROR';
				default:
					return 'ERROR';
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
			if ( ! isset($checks[$check->status])) {
				$checks[$check->status] = [];
			}
			$checks[$check->status][$check->id] = $check;
		}
		$this->template->checks = $checks;

		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}

}
