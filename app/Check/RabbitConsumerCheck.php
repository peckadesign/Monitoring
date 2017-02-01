<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property string $queues
 * @property string $minimumConsumerCount
 * @property string|NULL $lastConsumerCount
 * @property string|NULL $login
 * @property string|NULL $password
 */
class RabbitConsumerCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_RABBIT_CONSUMERS;
	}


	public function getterStatus(): int
	{
		$last = $this->getLastConsumerCount();
		if (empty($last)) {
			return ICheck::STATUS_ERROR;
		} else {
			$minimum = $this->getMinimumConsumerCount();

			foreach ($this->getQueues() as $k => $v) {
				if ( ! isset($last[$k]) || $last[$k] < $minimum[$k]) {
					return ICheck::STATUS_ALERT;
				}
			}

			return ICheck::STATUS_OK;
		}
	}


	public function getQueues(): array
	{
		return $this->strToArray($this->getRawProperty('queues'));
	}


	public function getMinimumConsumerCount(): array
	{
		return $this->strToArray($this->getRawProperty('minimumConsumerCount'));
	}


	public function getLastConsumerCount(): array
	{
		return ($value = $this->getRawProperty('lastConsumerCount')) !== NULL ? $this->strToArray($value) : [];
	}


	public function getTitle(): string
	{
		return 'Počet consumerů fronty';
	}


	private function strToArray(string $string): array
	{
		return array_map('trim', explode(',', $string));
	}


	public function getterStatusMessage(): string
	{
		return '';
	}
}
