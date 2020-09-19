<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Logout;

interface IFactory
{

	public function create(): Control;

}
