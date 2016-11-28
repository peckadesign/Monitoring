<?php

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

interface ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data);


	public function getCheck(): \Pd\Monitoring\Check\Check;


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form);

}
