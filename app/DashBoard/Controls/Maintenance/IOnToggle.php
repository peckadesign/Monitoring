<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Maintenance;

interface IOnToggle
{

	public function process(Control $control);
}
