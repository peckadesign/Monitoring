<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Settings;

class Control extends \Nette\Application\UI\Control
{

	public function render()
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}

}
