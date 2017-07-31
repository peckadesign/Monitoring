<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecks;

interface IFactory
{

	public function create(\Pd\Monitoring\Project\Project $project): Control;

}
