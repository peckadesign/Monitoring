<?php declare(strict_types = 1);

namespace Pd\Monitoring\Elasticsearch;

final class ChecksExporter
{

	/**
	 * @var \Kdyby\RabbitMq\IProducer
	 */
	private $producer;


	public function __construct(
		\Kdyby\RabbitMq\IProducer $producer
	) {
		$this->producer = $producer;
	}


	public function export(array $persisted, array $removed): void
	{
		foreach ($persisted as $check) {
			try {
				if ($check instanceof \Pd\Monitoring\Check\AliveCheck) {
					$export = new AliveCheck($check);
				} else {
					continue;
				}
			} catch (\InvalidArgumentException $e) {
				continue;
			}

			$this->producer->publish(\Nette\Utils\Json::encode($export));
		}
	}

}
