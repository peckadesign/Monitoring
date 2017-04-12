<?php

namespace Pd\Monitoring\DashBoard\Controls\Check;

interface IOnRedraw
{

	public function onRedraw(Control $control, \Pd\Monitoring\Check\Check $check);

}
