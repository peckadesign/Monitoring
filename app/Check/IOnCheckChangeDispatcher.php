<?php

namespace Pd\Monitoring\Check;

interface IOnCheckChangeDispatcher
{

	public function addListener(IOnCheckChange $onCheckChange): void;


	public function change(Check $check): void;

}
