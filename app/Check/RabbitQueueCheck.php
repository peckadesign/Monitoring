<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string|NULL $adminUrl
 * @property string $queues
 * @property string $maximumMessageCount
 * @property string|NULL $lastMessageCount
 * @property string|NULL $login
 * @property string|NULL $password
 * @property bool $validateHttps
 */
class RabbitQueueCheck extends Check
{

	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_RABBIT_QUEUES;
	}


	protected function getStatus(): int
	{
		$last = $this->getLastMessageCount();
		if (empty($last)) {
			return ICheck::STATUS_ERROR;
		} else {
			$maximum = $this->getMaximumMessageCount();

			foreach ($this->getQueues() as $k => $v) {
				if ( ! isset($last[$k]) || $last[$k] > $maximum[$k] * 2) {
					return ICheck::STATUS_ERROR;
				} elseif ($last[$k] > $maximum[$k]) {
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


	public function getMaximumMessageCount(): array
	{
		return $this->strToArray($this->getRawProperty('maximumMessageCount'));
	}


	public function getLastMessageCount(): array
	{
		return ($value = $this->getRawProperty('lastMessageCount')) !== NULL ? $this->strToArray($value) : [];
	}


	public function getTitle(): string
	{
		return 'Počet zpráv ve frontě';
	}


	private function strToArray(string $string): array
	{
		return \array_map('trim', \explode(',', $string));
	}


	public function getterStatusMessage(): string
	{
		$messages = [];

		$last = $this->getLastMessageCount();
		if ( ! $last) {
			$messages[] = 'Kontrola počtu zpráv selhala.';
		}

		$maximum = $this->getMaximumMessageCount();

		$translator = new class implements \Nette\Localization\ITranslator
		{

			function translate($message, $count = NULL)
			{
				switch ($count) {
					case 1:
						return $message . 'u';
					case 2:
					case 3:
					case 4:
						return $message . 'y';
					default:
						return $message;
				}
			}
		};

		foreach ($this->getQueues() as $k => $v) {
			if ( ! isset($maximum[$k])) {
				$messages[] = 'Pro frontu "' . $v . '" není nastavený maximální počet zpráv.';
			} elseif ($last[$k] > $maximum[$k]) {
				$messages[] = \sprintf('Fronta "%s" má %u %s, očekává se nanejvýš %u.', $v, $last[$k], $translator->translate('zpráv', $last[$k]), $maximum[$k]);
			}
		}

		return \implode(' ', $messages);
	}

}
