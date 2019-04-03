<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SubProjects;

final class Tab
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var bool
	 */
	private $selected;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		bool $selected
	) {
		$this->project = $project;
		$this->selected = $selected;
	}


	public function getProject(): \Pd\Monitoring\Project\Project
	{
		return $this->project;
	}


	public function isSelected(): bool
	{
		return $this->selected;
	}

}
