<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Favicons;

class Control extends \Nette\Application\UI\Control
{

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/Control.latte');
		$this->getTemplate()->render();
	}

}
