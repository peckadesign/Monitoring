<?php declare(strict_types = 1);

namespace Pd\Monitoring\Elasticsearch\LifetimePolicy;

class LifetimePolicyCommand extends \Symfony\Component\Console\Command\Command
{

	private const LIFETIME_POLICY_NAME = 'monitoring-lifetime-policy';

	private \Elasticsearch\Client $client;


	public function __construct(\Elasticsearch\Client $client)
	{
		parent::__construct();

		$this->client = $client;
	}


	public function configure()
	{
		$this->setName('elasticsearch:lifetime-policy:enable');
	}


	public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output): int
	{
		$lifetimePolicyFile = \Nette\Utils\FileSystem::read(__DIR__ . '/lifetime_policy.json');
		$this->client->ilm()->putLifecycle([
			'policy' => self::LIFETIME_POLICY_NAME,
			'body' => $lifetimePolicyFile
			]
		);
		$output->writeln('Lifetime policy \'' . self::LIFETIME_POLICY_NAME . '\' added.');


		return 0;
	}

}
