<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

class LoginPresenter extends \Nette\Application\UI\Presenter
{

	use \Pd\Monitoring\DashBoard\Controls\Favicons\TFactory;


	private \League\OAuth2\Client\Provider\Github $github;

	/**
	 * @persistent
	 */
	public string $backLink;


	public function __construct(
		\League\OAuth2\Client\Provider\Github $gitHub
	)
	{
		parent::__construct();
		$this->github = $gitHub;
	}


	public function handleLogin(): void
	{
		if ( ! $this->user->isLoggedIn()) {
			$authUrl = $this->github->getAuthorizationUrl(['state' => $this->backLink]);
			$this->redirectUrl($authUrl);
		}
	}

}
