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
		$from = new \DateTime('-' . ($howManyDaysBack + 1) . ' days');
		$to = new \DateTime('-' . $howManyDaysBack . ' days');
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
										'gte' => $from->format('Y-m-d H:00:00'),
										'lt' => $to->format('Y-m-d H:00:00'),
										'format' => 'yyyy-MM-dd HH:mm:ss',
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

		do {
			$return[$from->format('Y-m-d\TH:00:00.000\Z')] = 0;
			$from->add(new \DateInterval('PT1H'));
		} while ($from->format('Y-m-d H') < $to->format('Y-m-d H'));

		if (isset($results['aggregations'])) {
			foreach ($results['aggregations']['average_timeout']['buckets'] as $bucket) {
				$return[$bucket['key_as_string']] = $bucket['average_timeout']['value'];
			}
		}

		return $return;
	}

}
