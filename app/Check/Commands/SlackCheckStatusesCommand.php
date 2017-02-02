<?php

namespace Pd\Monitoring\Check\Commands;

class SlackCheckStatusesCommand extends \Symfony\Component\Console\Command\Command
{

	use \Pd\Monitoring\Commands\TNamedCommand;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Nette\Application\LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 */
	private $dateTimeProvider;

	/**
	 * @var string
	 */
	private $hookUrl;

	/**
	 * @var \Monolog\Logger
	 */
	private $logger;

	/**
	 * @var \Pd\Monitoring\Check\SlackNotifyLocksRepository
	 */
	private $slackNotifyLocksRepository;


	public function __construct(
		string $hookUrl,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Nette\Application\LinkGenerator $linkGenerator,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider,
		\Monolog\Logger $logger,
		\Pd\Monitoring\Check\SlackNotifyLocksRepository $slackNotifyLocksRepository
	) {
		parent::__construct();

		$this->checksRepository = $checksRepository;
		$this->linkGenerator = $linkGenerator;
		$this->dateTimeProvider = $dateTimeProvider;
		$this->hookUrl = $hookUrl;
		$this->logger = $logger;
		$this->slackNotifyLocksRepository = $slackNotifyLocksRepository;
	}


	protected function configure()
	{
		parent::configure();

		$this->setName($this->generateName());
	}


	protected function execute(
		\Symfony\Component\Console\Input\InputInterface $input,
		\Symfony\Component\Console\Output\OutputInterface $output
	) {
		$options = [
			'paused' => FALSE,
		];
		$checks = $this->checksRepository->findBy($options);

		foreach ($checks as $check) {

			$conditions = [
				'check' => $check,
			];
			$locks = $this->slackNotifyLocksRepository->findBy($conditions)->orderBy('locked', \Nextras\Orm\Collection\ICollection::DESC)->limitBy(1);
			/** @var \Pd\Monitoring\Check\SlackNotifyLock $lastLock */
			$lastLock = $locks->fetch();

			if ($lastLock && $lastLock->status === $check->status && $lastLock->locked >= $this->dateTimeProvider->getDateTime()->sub(new \DateInterval('PT60M'))) {
				continue;
			}

			$url = $this->linkGenerator->link('DashBoard:Project:', [$check->project->id]);

			switch ($check->status) {
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					$color = 'warning';
					break;
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					$color = 'danger';
					break;
				default:
					continue 2;
			}

			$lock = new \Pd\Monitoring\Check\SlackNotifyLock();
			$lock->locked = $this->dateTimeProvider->getDateTime();
			$lock->status = $check->status;
			$lock->check = $check;
			$this->slackNotifyLocksRepository->persistAndFlush($lock);

			$checkStatusMessage = $check->statusMessage;

			$message = sprintf(
				'Pro <%s|projekt %s> je zaznamenán problém v kontrole %s%s',
				$url,
				$check->project->name,
				$check->fullName,
				$checkStatusMessage ? ': ' . $checkStatusMessage : ''
			);

			$payload = [
				'channel' => '#monitoring',
				'username' => 'Monitoring',
				'icon_emoji' => ':eye:',
				'attachments' => [
					[
						'text' => $message,
						'color' => $color,
						'ts' => $this->dateTimeProvider->getDateTime()->format('U'),
					],
				],
			];

			$options = [
				'json' => $payload,
			];

			try {
				$client = new \GuzzleHttp\Client();
				$client->request('POST', $this->hookUrl, $options);
			} catch (\Throwable $e) {
				$this->logger->addError($e);
			}
		}

		return 0;
	}

}
