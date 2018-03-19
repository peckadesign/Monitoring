<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Commands;

use Pd\Monitoring\Check\ICheck;

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
	 * @var \Monolog\Logger
	 */
	private $logger;

	/**
	 * @var \Pd\Monitoring\Check\SlackNotifyLocksRepository
	 */
	private $slackNotifyLocksRepository;

	/**
	 * @var \Pd\Monitoring\Slack\Notifier
	 */
	private $slackNotifier;

	/**
	 * @var \Pd\Monitoring\User\UsersRepository
	 */
	private $userRepository;


	public function __construct(
		\Pd\Monitoring\Slack\Notifier $slackNotifier,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Nette\Application\LinkGenerator $linkGenerator,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider,
		\Monolog\Logger $logger,
		\Pd\Monitoring\Check\SlackNotifyLocksRepository $slackNotifyLocksRepository,
		\Pd\Monitoring\User\UsersRepository $usersRepository
	) {
		parent::__construct();

		$this->checksRepository = $checksRepository;
		$this->linkGenerator = $linkGenerator;
		$this->dateTimeProvider = $dateTimeProvider;
		$this->logger = $logger;
		$this->slackNotifyLocksRepository = $slackNotifyLocksRepository;
		$this->slackNotifier = $slackNotifier;
		$this->userRepository = $usersRepository;
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
		$now = $this->dateTimeProvider->getDateTime();
		$options = [
			'paused' => FALSE,
			'this->project->maintenance' => NULL,
		];

		$checks = $this->checksRepository->findBy($options);

		foreach ($checks as $check) {
			if ($check->project->pausedTo && $check->project->pausedFrom) {
				$timeFrom = explode(':', $check->project->pausedFrom);
				$pausedFrom = $this->dateTimeProvider->getDateTime()->setTime((int) $timeFrom[0], (int) $timeFrom[1]);

				$timeTo = explode(':', $check->project->pausedTo);
				$pausedTo = $this->dateTimeProvider->getDateTime()->setTime((int) $timeTo[0], (int) $timeTo[1]);

				if ($now >= $pausedFrom && $now <= $pausedTo) {
					continue;
				}
			}

			$url = $this->linkGenerator->link('DashBoard:Project:', [$check->project->id]);
			$checkStatusMessage = $check->statusMessage;

			if ($check->awaitingRecovery && $check->status === ICheck::STATUS_OK) {
				$message = sprintf(
					'Pro <%s|projekt %s> se kontrola vrátila do normálu: %s%s',
					$url,
					$check->project->name,
					$check->fullName,
					$checkStatusMessage ? ': ' . $checkStatusMessage : ''
				);

				$this->slackNotifier->notify($message, 'good');
				$check->awaitingRecovery = FALSE;
				$this->checksRepository->persistAndFlush($check);
				continue;
			}

			$conditions = [
				'check' => $check,
			];
			$locks = $this->slackNotifyLocksRepository->findBy($conditions)->orderBy('locked', \Nextras\Orm\Collection\ICollection::DESC)->limitBy(1);
			/** @var \Pd\Monitoring\Check\SlackNotifyLock $lastLock */
			$lastLock = $locks->fetch();

			if ($lastLock && $lastLock->status === $check->status && $lastLock->locked >= $this->dateTimeProvider->getDateTime()->sub(new \DateInterval('PT60M'))) {
				continue;
			}

			switch ($check->status) {
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					if ($check->onlyErrors) {
						continue 2;
					}
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
			$this->slackNotifyLocksRepository->persist($lock);

			$check->awaitingRecovery = TRUE;
			$this->checksRepository->persistAndFlush($check);

			$message = sprintf(
				'Pro <%s|projekt %s> je zaznamenán problém v kontrole %s%s',
				$url,
				$check->project->name,
				$check->fullName,
				$checkStatusMessage ? ': ' . $checkStatusMessage : ''
			);

			$buttons = [
				new \Pd\Monitoring\Slack\Button('pause', 'Pozastavit kontrolu', $this->linkGenerator->link('DashBoard:Slack:pause', [$check->id])),
			];

			if ($check->project->notifications) {
				$this->slackNotifier->notify('#monitoring', $message, $color, $buttons);
			}

			foreach ($check->project->userSlackNotifications as $user) {
				if (($slackId = $user->user->slackId)) {
					$this->slackNotifier->notify($user->user->slackId, $message, $color, $buttons);
				}
			}
		}

		return 0;
	}

}
