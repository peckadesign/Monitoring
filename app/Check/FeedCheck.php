<?php declare(strict_types = 1);

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

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function injectDateTimeProvider(\Kdyby\Clock\IDateTimeProvider $dateTimeProvider): void
	{
		$this->dateTimeProvider = $dateTimeProvider;
	}

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
			if ( ! $this->lastModified) {
				$errorMessage = 'se nepodařilo zjistit';
			} else {
				$errorMessage = sprintf(
					'je %s. Maximální povolené stáří %s %u %s',
					\Pd\Monitoring\Utils\Helpers::dateTime($this->lastModified),
					\Pd\Monitoring\Utils\Helpers::plural($this->maximumAge, 'je', 'je', 'jsou'),
					$this->maximumAge,
					\Pd\Monitoring\Utils\Helpers::plural($this->maximumAge, 'hodin', 'hodina', 'hodiny')
				);
			}
			$message[] = sprintf('Datum poslední změny %s.', $errorMessage);
		}
		if ( ! $this->lastSizeControl()) {
			if ( ! $this->lastSize) {
				$errorMessage = 'se nepodařilo zjistit';
			} else {
				$errorMessage = sprintf('je %s. Minimální musí být %s', \Latte\Runtime\Filters::bytes($this->lastSize), \Latte\Runtime\Filters::bytes($this->size * 1024 * 1024));
			}
			$message[] = sprintf('Velikost feedu %s.', $errorMessage);
		}

		return implode(' ', $message);
	}


	private function lastSizeControl(): bool
	{
		return $this->lastSize / 1024 / 1024 >= $this->size;
	}


	private function lastModifiedControl(): bool
	{
		return $this->lastModified >= $this->dateTimeProvider->getDateTime()->sub(new \DateInterval('PT' . $this->maximumAge . 'H'));
	}

}
