<?php declare(strict_types = 1);

namespace Tests\Pd\Monitoring\Check\Consumers\Client;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{

	public function testDefault(): void
	{
		$config = \Pd\Monitoring\Check\Consumers\Client\Configuration::create(10, 20)->config();

		$this->assertSame(['User-Agent' => 'PeckaMonitoringBot/1.0',], $config['headers']);
		$this->assertSame(TRUE, $config['verify']);
		$this->assertSame(10, $config['connect_timeout']);
		$this->assertSame(20, $config['timeout']);
	}


	/**
	 * @dataProvider dataProviderWithVerify
	 */
	public function testWithAll(int $timeout, int $connectionTimeout, bool $verify, array $allowRedirectsOptions): void
	{
		$allowRedirects = new \Pd\Monitoring\Check\Consumers\Client\Configuration\AllowRedirectsArray($allowRedirectsOptions);

		$config = \Pd\Monitoring\Check\Consumers\Client\Configuration::create($connectionTimeout, $timeout, $verify)
			->withAllowRedirects($allowRedirects)
			->config()
		;

		$this->assertSame(['User-Agent' => 'PeckaMonitoringBot/1.0',], $config['headers']);
		$this->assertSame($allowRedirectsOptions, $config['allow_redirects']);
		$this->assertSame($verify, $config['verify']);
		$this->assertSame($connectionTimeout, $config['connect_timeout']);
		$this->assertSame($timeout, $config['timeout']);
	}


	public function dataProviderWithVerify(): array
	{
		return [
			[
				10,
				20,
				TRUE,
				[
					'max' => 5,
					'protocols' => ['http', 'https',],
					'strict' => FALSE,
					'referer' => FALSE,
					'track_redirects' => FALSE,
				],
			],
			[
				10,
				20,
				FALSE,
				[],
			],
		];
	}

}
