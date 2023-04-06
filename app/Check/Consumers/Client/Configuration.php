<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers\Client;

/**
 * @see https://docs.guzzlephp.org/en/stable/request-options.html
 */
class Configuration
{

	private ?int $connectTimeout;

	private ?int $timeout;

	private bool $verify;

	private ?Configuration\AllowRedirectsInterface $allowRedirects;


	private function __construct(
		?int $connectTimeout,
		?int $timeout,
		bool $verify,
		?Configuration\AllowRedirectsInterface $allowRedirects
	)
	{
		$this->connectTimeout = $connectTimeout;
		$this->timeout = $timeout;
		$this->verify = $verify;
		$this->allowRedirects = $allowRedirects;
	}


	public function withAllowRedirects(Configuration\AllowRedirectsInterface $allowRedirects): self
	{
		return new self(
			$this->connectTimeout,
			$this->timeout,
			$this->verify,
			$allowRedirects
		) ;
	}


	public static function create(
		int $connectTimeout,
		int $timeout,
		bool $verify = TRUE,
		?Configuration\AllowRedirectsInterface $allowRedirects = NULL
	): self
	{
		return new self(
			$connectTimeout,
			$timeout,
			$verify,
			$allowRedirects
		);
	}


	/**
	 * @return array<string, string|array<string, string>>
	 */
	public function config(): array
	{
		$config = [
			'verify' => $this->verify,
			'connect_timeout' => $this->connectTimeout,
			'timeout' => $this->timeout,
		];

		$headers = [
			'headers' => [
				'User-Agent' => 'PeckaMonitoringBot/1.0',
			],
		];

		return \array_filter(
			\array_merge(
				$config,
				$headers,
				$this->allowRedirects !== NULL ? $this->allowRedirects->toArray() : []
			),
			static fn ($value) => ! \is_null($value)
		);
	}

}
