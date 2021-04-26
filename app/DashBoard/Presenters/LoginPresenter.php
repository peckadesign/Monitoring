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

	private \Pd\Monitoring\DashBoard\Forms\LoginFormFactory $loginFormFactory;


	public function __construct(
		\League\OAuth2\Client\Provider\Github $gitHub,
		\Pd\Monitoring\DashBoard\Forms\LoginFormFactory $loginFormFactory
	)
	{
		parent::__construct();
		$this->github = $gitHub;
		$this->loginFormFactory = $loginFormFactory;
	}


	public function handleLogin(): void
	{
		if ( ! $this->user->isLoggedIn()) {
			$authUrl = $this->github->getAuthorizationUrl(['state' => $this->backLink]);
			$this->redirectUrl($authUrl);
		}
	}


	protected function createComponentForm(): \Nette\Application\UI\Form
	{
		$form = $this->loginFormFactory->create();

		$form->addSubmit('login', 'Přihlásit se');
		$form->onSuccess[] = function (\Nette\Application\UI\Form $form, \Pd\Monitoring\DashBoard\Forms\LoginFormData $values): void
		{
			try {
				$this->getUser()->login($values->email, $values->password);

				$this->restoreRequest($this->backLink);
				$this->redirect('HomePage:');
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($e->getMessage());
			}
		};

		return $form;
	}

}
