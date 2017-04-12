<?php

namespace Pd\Monitoring\Project;

interface IOnProjectChange
{

	public function onProjectChange(Project $project): void;

}
