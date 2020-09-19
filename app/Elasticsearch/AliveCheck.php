<?php declare(strict_types = 1);

namespace Pd\Monitoring\Elasticsearch;

final class AliveCheck implements \JsonSerializable
{

	private \Pd\Monitoring\Check\Check $check;


	public function __construct(\Pd\Monitoring\Check\AliveCheck $check)
	{
		if ( ! $check->lastCheck) {
			throw new \InvalidArgumentException();
		}

		$this->check = $check;
	}


	public function jsonSerialize(): array
	{
		return [
			'check_id' => $this->check->id,
			'timeout' => $this->check->lastTimeout,
			'datetime' => $this->check->lastCheck->format(\DATE_W3C),
		];
	}

}
