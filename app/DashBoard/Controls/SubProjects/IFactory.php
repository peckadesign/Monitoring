<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SubProjects;

interface IFactory
{

	public function create(\Pd\Monitoring\Project\Project $project): Control;

}
