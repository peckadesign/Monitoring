<?php declare(strict_types = 1);

umask(0);

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

if (PHP_SAPI === 'cli') {
	Kdyby\Console\DI\BootstrapHelper::setupMode($configurator);
} else {
	$configurator->setDebugMode(TRUE);
}

$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
