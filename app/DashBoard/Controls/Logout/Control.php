<?php

namespace Pd\Monitoring\DashBoard\Controls\Logout;

use Nette;


class Control extends Nette\Application\UI\Control
{

	/**
	 * @var Nette\Security\User
	 */
	private $user;


	public function __construct(
		Nette\Security\User $user
	) {
		$this->user = $user;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	public function handleLogout()
	{
		$this->user->logout();
		$this->redirect('this');
	}
}
