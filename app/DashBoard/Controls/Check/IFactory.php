<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Check;

interface IFactory
{

	public function create(\Pd\Monitoring\Check\Check $check, bool $hasUserNotification): Control;

}
