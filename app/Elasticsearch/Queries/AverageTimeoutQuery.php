<?php declare(strict_types = 1);

namespace Pd\Monitoring\Elasticsearch\Queries;

final class AverageTimeoutQuery
{

	/**
	 * @var \Elasticsearch\Client
	 */
	private $elasticsearchClient;


	public function __construct(\Elasticsearch\Client $elasticsearchClient)
	{
		$this->elasticsearchClient = $elasticsearchClient;
	}


	public function query(int $checkId, int $howManyDaysBack): array
	{
		$params = [
			'index' => 'checks_*',
			'type' => 'checks',
			'body' => [
				'query' => [
					'bool' => [
						'must' => [
							[
								'term' => [
									'check_id' => $checkId,
								],
							],
							[
								'range' => [
									'datetime' => [
										'gte' => (new \DateTime('-' . $howManyDaysBack - 1 . ' days'))->format('Y-m-d H'),
										'lt' => (new \DateTime('-' . $howManyDaysBack . ' days'))->format('Y-m-d H'),
										'format' => 'YYYY-MM-DD HH',
									],
								],
							],
						],
					],
				],
				'size' => 0,
				'aggs' => [
					'average_timeout' => [
						'date_histogram' => [
							'field' => 'datetime',
							'interval' => 'hour',
						],
						'aggs' => [
							'average_timeout' => [
								'avg' => [
									'field' => 'timeout',
								],
							],
						],
					],
				],
			],
		];

		$results = $this->elasticsearchClient->search($params);

		$return = [];
		foreach ($results['aggregations']['average_timeout']['buckets'] as $bucket) {
			$return[$bucket['key_as_string']] = $bucket['average_timeout']['value'];
		}

		return $return;
	}

}
