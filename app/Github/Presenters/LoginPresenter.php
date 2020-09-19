<?php declare(strict_types = 1);

namespace Pd\Monitoring\Github\Presenters;

final class LoginPresenter extends \Nette\Application\UI\Presenter
{

	private \League\OAuth2\Client\Provider\Github $github;

	private \Pd\Monitoring\User\UsersRepository $users;


	public function __construct(
		\League\OAuth2\Client\Provider\Github $gitHub,
		\Pd\Monitoring\User\UsersRepository $users
	)
	{
		parent::__construct();
		$this->github = $gitHub;
		$this->users = $users;
	}


	public function actionDefault(string $code, string $state): void
	{
		try {
			$token = $this->github->getAccessToken('authorization_code', [
				'code' => $code,
			]);

			/** @var \League\OAuth2\Client\Provider\GithubResourceOwner $gitHubUser */
			$gitHubUser = $this->github->getResourceOwner($token);

			$conditions = [
				'gitHubId' => $gitHubUser->getId(),
			];
			$user = $this->users->getBy($conditions);
			if ( ! $user) {
				$user = new \Pd\Monitoring\User\User();
				$user->gitHubId = $gitHubUser->getId();
				$user->gitHubName = $gitHubUser->getName() ?: $gitHubUser->getNickname();
				$user->administrator = FALSE;
			}

			$user->gitHubToken = $token->getToken();
			$this->users->persistAndFlush($user);

			$this->getUser()->login($user);
		} catch (\Throwable $e) {
			$this->flashMessage('Přihlášení přes GitHub selhalo: ' . $e->getMessage(), \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_ERROR);
		}

		if ($state) {
			$this->restoreRequest($state);
		}

		$this->redirect(':DashBoard:HomePage:default');
	}

}
