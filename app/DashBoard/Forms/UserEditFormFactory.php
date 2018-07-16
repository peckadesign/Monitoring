<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms;

class UserEditFormFactory
{

	public const FIELD_GIT_HUB_NAME = 'gitHubName';
	public const FIELD_ADMINISTRATOR = 'administrator';
	public const FIELD_SLACK_ID = 'slackId';

	/**
	 * @var Factory
	 */
	private $factory;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	public function __construct(
		Factory $factory,
		\Nette\Security\User $user
	) {
		$this->factory = $factory;
		$this->user = $user;
	}


	public function create(): \Nette\Application\UI\Form
	{
		$form = $this->factory->create();

		$form->addText(self::FIELD_GIT_HUB_NAME, 'Jméno');
		$userNameDescription = 'Uživatelské ID musí začínat na @U';
		$form
			->addText(self::FIELD_SLACK_ID, 'Slack ID')
			->setRequired(FALSE)
			->addRule(\Nette\Forms\Form::PATTERN, $userNameDescription, '@U.+')
			->setOption('description', $userNameDescription)
		;

		if ($this->user->isAllowed('user', 'edit')) {
			$form->addCheckbox(self::FIELD_ADMINISTRATOR, 'Administrátor');
		}

		$form->addSubmit('save', 'Uložit');

		return $form;
	}

}
