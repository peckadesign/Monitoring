<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $url
 * @property float $size
 * @property int|NULL $lastSize
 * @property int $maximumAge
 * @property \DateTime|NULL $lastModified
 */
class FeedCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_FEED;
	}


	protected function getStatus(): int
	{
		if ($this->lastModified === NULL || $this->lastSize === NULL) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->lastSizeControl() && $this->lastModifiedControl()) {
				return ICheck::STATUS_OK;
			} else {
				return ICheck::STATUS_ALERT;
			}
		}
	}


	public function getTitle(): string
	{
		return 'Existence feedu';
	}


	public function getterStatusMessage(): string
	{
		$message = [];
		if ( ! $this->lastModifiedControl()) {
			$message[] = sprintf('Datum poslední změny %s.', ( ! $this->lastModified ? 'se nepodařilo zjistit' : 'je ' . $this->lastModified->format('Y-m-d H:i:s')));
		}
		if ( ! $this->lastSizeControl()) {
			$message[] = sprintf('Velikost feedu %s.', ( ! $this->lastSize ? 'se nepodařilo zjistit' : 'je ' . \Latte\Runtime\Filters::bytes($this->lastSize)));
		}

		return implode(' ', $message);
	}


	private function lastSizeControl(): bool
	{
		return $this->lastSize / 1024 / 1024 >= $this->size;
	}


	private function lastModifiedControl(): bool
	{
		return $this->lastModified >= (new \DateTime())->modify('-' . $this->maximumAge . ' hours');
	}
}
