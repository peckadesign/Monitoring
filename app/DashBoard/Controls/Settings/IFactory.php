<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Settings;

interface IFactory
{

	public function create(): Control;

}
