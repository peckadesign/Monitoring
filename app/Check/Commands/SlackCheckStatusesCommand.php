<?php declare(strict_types = 1);

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
	 * @var \Pd\Monitoring\Check\SlackNotifyLocksRepository
	 */
	private $slackNotifyLocksRepository;

	/**
	 * @var \Pd\Monitoring\Slack\Notifier
	 */
	private $slackNotifier;

	/**
	 * @var \Pd\Monitoring\Project\ProjectsRepository
	 */
	private $projectsRepository;


	public function __construct(
		\Pd\Monitoring\Slack\Notifier $slackNotifier,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Nette\Application\LinkGenerator $linkGenerator,
		\Kdyby\Clock\IDateTimeProvider $dateTimeProvider,
		\Pd\Monitoring\Check\SlackNotifyLocksRepository $slackNotifyLocksRepository
	) {
		parent::__construct();

		$this->checksRepository = $checksRepository;
		$this->linkGenerator = $linkGenerator;
		$this->dateTimeProvider = $dateTimeProvider;
		$this->slackNotifyLocksRepository = $slackNotifyLocksRepository;
		$this->slackNotifier = $slackNotifier;
		$this->projectsRepository = $projectsRepository;
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
			'this->project->maintenance' => NULL,
			'this->project->reference' => TRUE,
		];
		$referenceChecks = $this->checksRepository->findBy($options);

		$allPassed = TRUE;
		foreach ($referenceChecks as $referenceCheck) {
			if ($referenceCheck->status !== \Pd\Monitoring\Check\ICheck::STATUS_OK) {
				$allPassed = FALSE;
				break;
			}
		}

		if ( ! $allPassed) {
			$this->slackNotifier->notify('#monitoring', 'Některé referenční kontroly selhaly, neproběhne notifikace ostatních kontrol', 'warning', []);
			foreach ($referenceChecks as $referenceCheck) {
				if ($referenceCheck->status === \Pd\Monitoring\Check\ICheck::STATUS_OK) {
					continue;
				}
				$this->processCheck($referenceCheck);
			}
		} else {
			$options = [
				'maintenance' => NULL,
			];
			$projects = $this->projectsRepository->findBy($options);

			foreach ($projects as $project) {
				$options = [
					'paused' => FALSE,
					'reference' => TRUE,
					'project' => $project,
				];
				$referenceChecksForProject = $this->checksRepository->findBy($options);
				foreach ($referenceChecksForProject as $referenceCheckForProject) {
					if ($referenceCheckForProject->status !== \Pd\Monitoring\Check\ICheck::STATUS_OK) {
						$this->slackNotifier->notify(
							'#monitoring',
							\sprintf(
								'Některé referenční kontroly pro projekt %s selhaly, neproběhne notifikace ostatních kontrol',
								$referenceCheckForProject->project->name
							),
							'warning',
							[]
						);
						$this->processCheck($referenceCheckForProject);
						continue 2;
					}
				}
				foreach ($project->checks as $check) {
					$this->processCheck($check);
				}
			}
		}

		return 0;
	}


	private function processCheck(\Pd\Monitoring\Check\Check $check)
	{
		if ($check->project->parent && $check->project->parent->maintenance) {
			return;
		}

		if ($check->isPaused() || $check->project->isPaused()) {
			return;
		}

		$conditions = [
			'check' => $check,
		];
		$locks = $this->slackNotifyLocksRepository->findBy($conditions)->orderBy('locked', \Nextras\Orm\Collection\ICollection::DESC)->limitBy(1);
		/** @var \Pd\Monitoring\Check\SlackNotifyLock $lastLock */
		$lastLock = $locks->fetch();

		if ($lastLock && $lastLock->status === $check->status && $lastLock->locked >= $this->dateTimeProvider->getDateTime()->sub(new \DateInterval('PT60M'))) {
			return;
		}

		$color = 'good';
		switch ($check->status) {
			case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
				if ($check->onlyErrors) {
					return;
				}
				$color = 'warning';
				break;
			case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
				$color = 'danger';
				break;
			default:
				return;
		}

		$url = $this->linkGenerator->link('DashBoard:Project:', [$check->project->id]);

		$lock = new \Pd\Monitoring\Check\SlackNotifyLock();
		$lock->locked = $this->dateTimeProvider->getDateTime();
		$lock->status = $check->status;
		$lock->check = $check;
		$this->slackNotifyLocksRepository->persistAndFlush($lock);

		$checkStatusMessage = $check->statusMessage;

		$message = \sprintf(
			'Pro <%s|projekt %s> je zaznamenán problém v kontrole %s%s',
			$url,
			$check->project->name,
			$check->fullName,
			$checkStatusMessage ? ': ' . $checkStatusMessage : ''
		);

		$buttons = [
			new \Pd\Monitoring\Slack\Button('pause', 'Pozastavit kontrolu', $this->linkGenerator->link('DashBoard:Slack:pause', [$check->id])),
		];

		if ($check instanceof \Pd\Monitoring\Check\RabbitConsumerCheck || $check instanceof \Pd\Monitoring\Check\RabbitQueueCheck) {
			$buttons[] = new \Pd\Monitoring\Slack\Button('rabbitMqAdmin', 'Administrace RabbitMQ', $check->adminUrl);
		}

		if ($check->project->notifications && ( ! $check->project->parent || $check->project->parent->notifications)) {
			$this->slackNotifier->notify('#monitoring', $message, $color, $buttons);
		}

		$projectForUserSlackNotifications = $check->project->parent ?: $check->project;

		foreach ($projectForUserSlackNotifications->userSlackNotifications as $user) {
			if (($slackId = $user->user->slackId)) {
				$this->slackNotifier->notify($user->user->slackId, $message, $color, $buttons);
			}
		}
	}

}
