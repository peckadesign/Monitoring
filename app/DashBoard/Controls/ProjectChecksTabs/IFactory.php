<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs;

interface IFactory
{

	public function create(\Pd\Monitoring\Project\Project $project, int $type): Control;

}
