<?php

namespace Pd\Monitoring\Monolog\Handlers;

use Kdyby;
use Monolog;
use Nette;


/**
 * Handler exportuje zprávy do souborů, kdy pro každý den zakládá nový soubor a pro každý měsíc nový adresář
 *
 *  - Výsledná cesta je %logDir%/názevKanálu/YYYY-MM/YYYY-MM-DD-názevKanálu.log
 *  - Adresář pro uložení souboru se vytváří automaticky
 */
class DayFileHandler extends Kdyby\Monolog\Handler\FallbackNetteHandler
{

	/**
	 * @var \DateTime
	 */
	private $dateTime;

	/**
	 * @var string
	 */
	private $logDir;


	public function __construct($appName, $logDir, $expandNewlines = FALSE, Kdyby\Clock\IDateTimeProvider $dateTimeProvider)
	{
		parent::__construct($appName, $logDir, $expandNewlines);

		$this->dateTime = $dateTimeProvider->getDateTime();
		$this->logDir = $logDir;
	}


	protected function write(array $record)
	{
		$record['filename'] = $record['filename'] . '/' . $this->dateTime->format('Y-m') . '/' . $this->dateTime->format('Y-m-d') . '-' . $record['filename'];

		$logDirectory = dirname($this->logDir . '/' . strtolower($record['filename']));
		Nette\Utils\FileSystem::createDir($logDirectory);

		parent::write($record);
	}

}
