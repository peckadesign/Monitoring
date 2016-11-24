<?php

namespace Pd\Monitoring\DashBoard\Controls\Check;

interface IFactory
{
	public function create(\Pd\Monitoring\Check\Check $check) : Control;
}
