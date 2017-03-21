<?php

namespace Pd\Monitoring\DashBoard\Controls\Project;

interface IFactory
{
	public function create(\Pd\Monitoring\Project\Project $project, \Pd\Monitoring\UsersFavoriteProject\UsersFavoriteProject $favoriteProject = NULL) : Control;
}
