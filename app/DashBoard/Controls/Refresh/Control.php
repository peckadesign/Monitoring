<?php

namespace Pd\Monitoring\DashBoard\Controls\Refresh;

class Control extends \Nette\Application\UI\Control
{

	public function render()
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}
}
