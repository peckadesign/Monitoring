<?php

namespace Pd\Monitoring\DashBoard\Presenters;

use Pd;
use Kdyby;
use Nette;
use Tracy;


class LoginPresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var Kdyby\Github\Client
	 */
	private $github;

	/**
	 * @var string
	 * @persistent
	 */
	public $backLink;

	/**
	 * @var Pd\Monitoring\User\UsersRepository
	 */
	private $users;


	public function __construct(
		Kdyby\Github\Client $gitHub,
		Pd\Monitoring\User\UsersRepository $users
	) {
		parent::__construct();
		$this->github = $gitHub;
		$this->users = $users;
	}


	protected function createComponentGitHubLogin() : Kdyby\Github\UI\LoginDialog
	{
		$dialog = new Kdyby\Github\UI\LoginDialog($this->github);

		$dialog->onResponse[] = function (Kdyby\Github\UI\LoginDialog $dialog) {
			/** @var Kdyby\Github\Client $gitHub */
			$gitHub = $dialog->getClient();

			if ( ! $gitHub->getUser()) {
				$this->flashMessage("Sorry bro, github authentication failed.");

				return;
			}

			try {
				$me = $gitHub->api('/user');

				$conditions = [
					'gitHubId' => $gitHub->getUser(),
				];
				if ( ! $user = $this->users->getBy($conditions)) {
					$user = new Pd\Monitoring\User\User();
					$user->gitHubId = $me['id'];
					$user->gitHubName = $me['name'];
				}
				$user->gitHubToken = $gitHub->getAccessToken();
				$this->users->persistAndFlush($user);

				$this->getUser()->login($user);
			} catch (Kdyby\Github\ApiException $e) {

				Tracy\Debugger::log($e, 'github');
				$this->flashMessage("Sorry bro, github authentication failed hard.");
			}

			if ($this->backLink) {
				$this->restoreRequest($this->backLink);
			}

			$this->redirect(':DashBoard:HomePage:default');
		};

		return $dialog;
	}
}
