<?php

namespace Pd\Monitoring\DashBoard\Controls\Maintenance;

interface IFactory
{

	public function create(\Pd\Monitoring\Project\Project $project): Control;
}
