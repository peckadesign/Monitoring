<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms;

final class LoginFormFactory
{

	public const CONTROL_EMAIL = 'email';
	public const CONTROL_PASSWORD = 'password';


	private Factory $factory;


	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}


	public function create(): \Nette\Application\UI\Form
	{
		$form = $this->factory->create();

		$form->setMappedType(LoginFormData::class);

		$form
			->addEmail(self::CONTROL_EMAIL, 'E-mail')
			->setRequired(TRUE)
		;

		$form
			->addPassword(self::CONTROL_PASSWORD, 'Heslo')
			->setRequired(TRUE)
		;

		return $form;
	}

}
