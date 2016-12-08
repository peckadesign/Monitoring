<?php

require_once __DIR__ . '/../app/bootstrap.php';

$webId = WEB_ID;

$products = [];
foreach ($container->getByType(\Pd\Product\ParameterService::class)->fetchAllByFilter() as $parameter) {
	$products = array_merge($products, explode('|', $parameter->products));
}

$products = array_unique($products);

echo count($products);