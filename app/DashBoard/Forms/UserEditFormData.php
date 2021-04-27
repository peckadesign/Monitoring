<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms;

final class UserEditFormData
{

	public string $gitHubName;

	public ?string $slackId = NULL;

	public ?bool $administrator = NULL;

	public ?string $email = NULL;

	public ?string $password = NULL;

}
