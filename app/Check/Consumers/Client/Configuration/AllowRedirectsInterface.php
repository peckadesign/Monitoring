<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers\Client\Configuration;

interface AllowRedirectsInterface
{

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array;

}
