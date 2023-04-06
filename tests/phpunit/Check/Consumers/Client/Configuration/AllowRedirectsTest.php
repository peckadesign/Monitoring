<?php declare(strict_types = 1);

namespace Tests\Pd\Monitoring\Check\Consumers\Client\Configuration;

class AllowRedirectsTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @dataProvider dataProvider
	 */
	public function testToArray(bool $isAllowed): void
	{
		$allowRedirects = \Pd\Monitoring\Check\Consumers\Client\Configuration\AllowRedirects::create($isAllowed);
		$this->assertSame([
			'allow_redirects' => $isAllowed,
		], $allowRedirects->toArray());
	}


	public function dataProvider(): array
	{
		return [
			[
				'isAllowed' => TRUE,
			],
			[
				'isAllowed' => FALSE,
			],
		];
	}

}
