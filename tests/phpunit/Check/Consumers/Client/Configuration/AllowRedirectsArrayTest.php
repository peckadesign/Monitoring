<?php declare(strict_types = 1);

namespace Tests\Pd\Monitoring\Check\Consumers\Client\Configuration;

class AllowRedirectsArrayTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @dataProvider dataProvider
	 */
	public function testToArray(array $conf): void
	{
		$allowRedirects = new \Pd\Monitoring\Check\Consumers\Client\Configuration\AllowRedirectsArray($conf);
		$this->assertSame([
			'allow_redirects' => $conf,
		], $allowRedirects->toArray());
	}


	public function dataProvider(): array
	{
		return [
			[
				'conf' => [
					'max' => 10,
					'strict' => true,
					'referer' => true,
					'protocols' => ['https'],
					'track_redirects' => true
				],
			],
		];
	}

}
