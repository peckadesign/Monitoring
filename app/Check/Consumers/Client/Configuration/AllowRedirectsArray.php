<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers\Client\Configuration;

class AllowRedirectsArray implements AllowRedirectsInterface
{

	/**
	 * @var array<string, mixed>
	 */
	private array $conf;


	/**
	 * @param array<string, mixed> $conf
	 */
	public function __construct(array $conf)
	{
		$this->conf = $conf;
	}


	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [
			'allow_redirects' => $this->conf,
		];
	}

}
