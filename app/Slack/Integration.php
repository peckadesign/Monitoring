<?php declare(strict_types = 1);

namespace Pd\Monitoring\Slack;

/**
 * @property int $id {primary}
 * @property string $name
 * @property string $hookUrl
 * @property string $channel
 */
class Integration extends \Nextras\Orm\Entity\Entity
{

	private const DEFAULT_CHANNEL = '#monitoring';


	protected function getterChannel(?string $channel): string
	{
		if ($channel !== NULL) {
			return $channel;
		}

		return self::DEFAULT_CHANNEL;
	}


	public function getFullName(): string
	{
		return $this->name . \sprintf(' (%s)', $this->channel);
	}

}
