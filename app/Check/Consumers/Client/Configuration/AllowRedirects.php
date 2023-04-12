<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers\Client\Configuration;

class AllowRedirects implements AllowRedirectsInterface
{

	private bool $isAllowed;


	private function __construct(bool $isAllowed)
	{
		$this->isAllowed = $isAllowed;
	}


	/**
	 * @return array<string, bool>
	 */
	public function toArray(): array
	{
		return [
			'allow_redirects' => $this->isAllowed,
		];
	}


	public static function create(bool $isAllowed): self
	{
		return new self($isAllowed);
	}

}
