<?php declare(strict_types = 1);

namespace Pd\Monitoring\Github;

class GitHubLogin
{

	private bool $isAllowed;


	public function __construct(bool $isAllowed = TRUE)
	{
		$this->isAllowed = $isAllowed;
	}


	public function isAllowed(): bool
	{
		return $this->isAllowed;
	}

}
