<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AliveChart;

interface IFactory
{

	public function create(\Pd\Monitoring\Check\AliveCheck $aliveCheck): Control;

}
