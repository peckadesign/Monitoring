<?php

namespace Pd\Monitoring\Check;

interface IOnCheckChange
{

	public function onCheckChange(Check $check): void;

}
