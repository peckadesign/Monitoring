<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SlackIntegrationList;

class Control extends \Nette\Application\UI\Control
{

	use \Pd\Monitoring\DashBoard\TSecuredComponent;


	private \Pd\Monitoring\Slack\IntegrationRepository $slackIntegrationRepository;

	private \Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory;


	public function __construct(
		\Pd\Monitoring\Slack\IntegrationRepository $slackIntegrationRepository,
		\Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory
	)
	{
		$this->slackIntegrationRepository = $slackIntegrationRepository;
		$this->dataGridFactory = $dataGridFactory;
	}


	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	protected function createComponentListGrid(): \Nette\Application\UI\Control
	{
		$grid = $this->dataGridFactory->create();

		$grid
			->addColumnText('name', 'Jméno')
			->setFilterText(['name'])
		;

		$grid
			->addColumnText('hookUrl', 'Hook URL')
		;

		$grid
			->addColumnText('channel', 'Kanál')
			->setFilterText(['channel'])
		;

		$grid
			->addAction('edit', 'Upravit')
			->setClass('btn btn-warning')
		;

		$grid
			->addAction('delete', 'Smazat')
			->setClass('btn btn-danger')
			->setDataAttribute('confirm', '')
		;

		$grid->setDataSource($this->slackIntegrationRepository->findAll());

		return $grid;
	}


	public function handleEdit(int $id): void
	{
		$integration = $this->slackIntegrationRepository->getById($id);
		$this->getPresenter()->redirect(':DashBoard:Slack:edit', $integration);
	}


	/**
	 * @Acl(user, delete)
	 * @throws \Nette\Application\AbortException
	 */
	public function handleDelete(int $id): void
	{
		$this->slackIntegrationRepository->removeAndFlush($this->slackIntegrationRepository->getById($id));
		$this->getPresenter()->flashMessage('Slack integrace byla smazána', \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		$this->redirect('this');
	}

}
