<?php

namespace Pd\Monitoring\DashBoard\Controls\LastRefresh;

interface IFactory
{

	public function create(): Control;
}
