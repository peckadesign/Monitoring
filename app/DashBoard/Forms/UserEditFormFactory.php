<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms;

class UserEditFormFactory
{

	public const FIELD_GIT_HUB_NAME = 'gitHubName';
	public const FIELD_ADMINISTRATOR = 'administrator';
	public const FIELD_SLACK_ID = 'slackId';
	public const FIELD_EMAIL = 'email';
	public const FIELD_PASSWORD = 'password';


	private Factory $factory;

	private \Nette\Security\User $user;


	public function __construct(
		Factory $factory,
		\Nette\Security\User $user
	)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	public function create(): \Nette\Application\UI\Form
	{
		$form = $this->factory->create();

		$form->setMappedType(UserEditFormData::class);

		$form
			->addText(self::FIELD_GIT_HUB_NAME, 'Jméno')
			->setRequired()
		;

		$userNameDescription = 'Uživatelské ID musí začínat na @U';
		$form
			->addText(self::FIELD_SLACK_ID, 'Slack ID')
			->setRequired(FALSE)
			->setNullable()
			->addRule(\Nette\Forms\Form::PATTERN, $userNameDescription, '@U.+')
			->setOption('description', $userNameDescription . '. Získáte jej v aplikaci v "Profile & account" > "More actions" > "Copy member ID"')
		;

		if ($this->user->isAllowed(\Pd\Monitoring\User\AclFactory::RESOURCE_USER, \Pd\Monitoring\User\AclFactory::PRIVILEGE_EDIT)) {
			$form->addCheckbox(self::FIELD_ADMINISTRATOR, 'Administrátor');
		}

		$form
			->addEmail(self::FIELD_EMAIL, 'E-mail')
			->setRequired(TRUE)
		;

		$form
			->addPassword(self::FIELD_PASSWORD, 'Heslo')
			->setNullable()
		;

		$form->addSubmit('save', 'Uložit');

		return $form;
	}

}
