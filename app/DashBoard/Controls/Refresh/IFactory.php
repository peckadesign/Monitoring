<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Refresh;

interface IFactory
{

	public function create(): Control;
}
