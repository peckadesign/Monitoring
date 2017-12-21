<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

final class SlackPresenter extends BasePresenter
{

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\Slack\Notifier
	 */
	private $notifier;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\Slack\Notifier $notifier
	) {
		parent::__construct();
		$this->checksRepository = $checksRepository;
		$this->notifier = $notifier;
	}


	public function actionPause(int $id): void
	{
		$check = $this->checksRepository->getById($id);

		if ( ! $check) {
			$this->error();
		}

		$check->paused = TRUE;
		$this->checksRepository->persistAndFlush($check);

		$this->flashMessage('Kontrola byla zapauzována', self::FLASH_MESSAGE_SUCCESS);

		$this->redirect(':DashBoard:Project:', [$check->project->id]);
	}

}
