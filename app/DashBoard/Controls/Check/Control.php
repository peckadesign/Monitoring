<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Check;

class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Check\Check $check;

	private \Pd\Monitoring\Check\ChecksRepository $checksRepository;

	private \Kdyby\RabbitMq\Connection $rabbitConnection;

	private \Pd\Monitoring\DashBoard\Controls\AliveChart\IFactory $aliveChartControlFactory;

	private \Pd\Monitoring\UserCheckNotifications\UserCheckNotificationsRepository $userCheckNotificationsRepository;

	private bool $hasUserNotification;

	private \Pd\Monitoring\User\User $user;

	private \Pd\Monitoring\DashBoard\Controls\LogView\Factory $logViewFactory;


	public function __construct(
		\Pd\Monitoring\Check\Check $check,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\RabbitMq\Connection $rabbitConnection,
		\Pd\Monitoring\DashBoard\Controls\AliveChart\IFactory $aliveChartControlFactory,
		\Pd\Monitoring\UserCheckNotifications\UserCheckNotificationsRepository $userCheckNotificationsRepository,
		bool $hasUserNotification,
		\Pd\Monitoring\User\User $user,
		\Pd\Monitoring\DashBoard\Controls\LogView\Factory $logViewFactory
	)
	{
		$this->check = $check;
		$this->checksRepository = $checksRepository;
		$this->rabbitConnection = $rabbitConnection;
		$this->aliveChartControlFactory = $aliveChartControlFactory;
		$this->userCheckNotificationsRepository = $userCheckNotificationsRepository;
		$this->hasUserNotification = $hasUserNotification;
		$this->user = $user;
		$this->logViewFactory = $logViewFactory;
	}


	protected function createTemplate(): \Nette\Application\UI\ITemplate
	{
		/** @var \Latte\Runtime\Template $template */
		$template = parent::createTemplate();

		$template->addFilter('dateTime', static function (\DateTimeImmutable $value)
		{
			return $value->format('j. n. Y H:i:s');
		});

		return $template;
	}


	public function render(): void
	{
		$this
			->template
			->setFile(__DIR__ . '/Control.latte')
			->add('check', $this->check)
			->add('hasUserNotification', $this->hasUserNotification)
			->render()
		;
	}


	public function handleDelete(): void
	{
		$this->checksRepository->removeAndFlush($this->check);
		$this->getPresenter()->flashMessage('Kontrola byla odebrána', \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		$this->redirect('this');
	}


	public function handlePause(): void
	{
		$this->check->paused = ! $this->check->paused;
		$this->checksRepository->persistAndFlush($this->check);

		$this->processRequest();
	}


	public function handleRefresh(): void
	{
		$this->rabbitConnection->getProducer($this->check->getProducerName())->publish((string) $this->check->id);

		$this->processRequest();
	}


	private function processRequest(): void
	{
		if ($this->getPresenter()->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}


	protected function createComponentAliveChart(): \Pd\Monitoring\DashBoard\Controls\AliveChart\Control
	{
		return $this->aliveChartControlFactory->create($this->check);
	}


	public function handleUserNotificationOn(): void
	{
		$notification = new \Pd\Monitoring\UserCheckNotifications\UserCheckNotifications();
		$notification->user = $this->user;
		$notification->check = $this->check;
		$this->userCheckNotificationsRepository->persistAndFlush($notification);
		$this->hasUserNotification = TRUE;

		$this->processRequest();
	}


	public function handleUserNotificationOff(): void
	{
		$this->userCheckNotificationsRepository->deleteUserCheckNotifications($this->user, $this->check);
		$this->hasUserNotification = FALSE;

		$this->processRequest();
	}


	protected function createComponentLogView(): \Ublaboo\DataGrid\DataGrid
	{
		return $this->logViewFactory->create($this->check);
	}

}
