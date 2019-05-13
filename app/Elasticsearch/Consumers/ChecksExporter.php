<?php declare(strict_types = 1);

namespace Pd\Monitoring\Elasticsearch\Consumers;

final class ChecksExporter implements \Kdyby\RabbitMq\IConsumer
{

	/**
	 * @var \Elasticsearch\Client
	 */
	private $elasticsearchClient;


	public function __construct(\Elasticsearch\Client $elasticsearchClient)
	{
		$this->elasticsearchClient = $elasticsearchClient;
	}


	public function process(\PhpAmqpLib\Message\AMQPMessage $message): int
	{
		$index = 'checks_' . \date('Y_m_d');
		$type = '_doc';

		$params = [
			'index' => $index,
		];
		try {
			$this->elasticsearchClient->indices()->stats($params);
		} catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
			$params = [
				'index' => $index,
				'body' => [
					'mappings' => [
						'_source' => [
							'enabled' => TRUE,
						],
						'properties' => [
							'datetime' => [
								'type' => 'date',
							],
							'check_id' => [
								'type' => 'keyword',
							],
							'timeout' => [
								'type' => 'float',
							],
						],
					],
				],
			];
			$this->elasticsearchClient->indices()->create($params);
		}

		$params = [
			'index' => $index,
			'type' => $type,
			'body' => \Nette\Utils\Json::decode($message->getBody(), \Nette\Utils\Json::FORCE_ARRAY),
		];
		$this->elasticsearchClient->index($params);

		return \Kdyby\RabbitMq\IConsumer::MSG_ACK;
	}

}
