<?php declare(strict_types = 1);

namespace Pd\Monitoring\Elasticsearch;

final class ClientFactory
{

	public static function create(array $hosts): \Elasticsearch\Client
	{
		$client = \Elasticsearch\ClientBuilder::create();

		$client->setHosts($hosts);

		return $client->build();
	}

}
