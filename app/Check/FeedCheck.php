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


	public function getterStatus(): int
	{
		if ($this->lastModified === NULL || $this->lastSize === NULL) {
			return ICheck::STATUS_ERROR;
		} else {
			if ($this->lastSize/1024/1024 >= $this->size && $this->lastModified >= (new \DateTime())->modify('-' . $this->maximumAge . ' hours')) {
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
		return '';
	}
}
