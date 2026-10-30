<?php declare(strict_types = 1);

namespace Tests\Pd\Monitoring\Check\Consumers\Client;

class HeadersTest extends \PHPUnit\Framework\TestCase
{

	public function testDefaults(): void
	{
		$this->assertSame(
			[
				'User-Agent' => 'PeckaMonitoringBot/1.0',
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			],
			\Pd\Monitoring\Check\Consumers\Client\Headers::defaults()
		);
	}


	public function testLines(): void
	{
		$this->assertSame(
			[
				'User-Agent: PeckaMonitoringBot/1.0',
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			],
			\Pd\Monitoring\Check\Consumers\Client\Headers::lines()
		);
	}

}
