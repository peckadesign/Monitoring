<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Logout;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(
		\Nette\Security\User $user
	) {
		parent::__construct();
		$this->user = $user;
	}


	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	public function handleLogout(): void
	{
		$this->user->logout();
		$this->redirect('this');
	}
}
