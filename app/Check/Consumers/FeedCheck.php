<?php declare(strict_types=1);

namespace Pd\Monitoring\Check\Consumers;

class FeedCheck extends Check
{

	/**
	 * @var \Monolog\Logger
	 */
	private $logger;

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider,
		\Pd\Monitoring\Orm\Orm $orm,
		\Monolog\Logger $logger
	) {
		parent::__construct($checksRepository, $dateTimeProvider, $orm);

		$this->dateTimeProvider = $dateTimeProvider;
		$this->logger = $logger;
	}


	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\FeedCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$this->logger->addInfo(
			sprintf(
				'Proběhne kontrola feedu %s (%s) pro projekt %s',
				$check->url,
				$check->fullName,
				$check->project->name
			)
		);

		$ch = curl_init($check->url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);    // we want headers
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);    // we don't need body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$output = curl_exec($ch);

		$this->logger->addInfo('Stažené hlavičky pro stavový kód ' . curl_getinfo($ch, CURLINFO_HTTP_CODE), ['headers' => $output]);

		$check->lastModified = NULL;
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
			$check->lastSize = NULL;
		} else {
			$check->lastSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

			foreach (explode("\n", $output) as $line) {
				if (stripos($line, 'last-modified:') !== FALSE) {
					$check->lastModified = new \DateTimeImmutable(trim(str_ireplace('last-modified:', '', $line)));
					break;
				}
			}
		}
		curl_close($ch);

		return $check->lastSize ? TRUE : FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_FEED;
	}
}
