<?php declare(strict_types = 1);

namespace Pd\Monitoring\Github;

final class ProviderFactory
{

	private string $clientId;

	private string $clientSecret;

	/**
	 * @var array<string>
	 */
	private array $scope;


	/**
	 * @param array<string> $scope
	 */
	public function __construct(
		string $clientId,
		string $clientSecret,
		array $scope
	)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->scope = $scope;
	}


	public function create(): \League\OAuth2\Client\Provider\Github
	{
		$options = [
			'clientId' => $this->clientId,
			'clientSecret' => $this->clientSecret,
			'scope' => $this->scope,
		];

		return new \League\OAuth2\Client\Provider\Github($options);
	}

}
