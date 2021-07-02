<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\SlackIntegrationOnProject;

final class Factory
{

	private \Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory;

	private \Pd\Monitoring\Slack\IntegrationRepository $integrationRepository;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory,
		\Pd\Monitoring\Slack\IntegrationRepository $integrationRepository,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository
	)
	{
		$this->dataGridFactory = $dataGridFactory;
		$this->integrationRepository = $integrationRepository;
		$this->projectsRepository = $projectsRepository;
	}


	public function create(\Pd\Monitoring\Project\Project $project): \Ublaboo\DataGrid\DataGrid
	{
		$dataGrid = $this->dataGridFactory->create();

		$dataGrid->setPrimaryKey('id');

		$dataSource = new \Ublaboo\DataGrid\DataSource\NextrasDataSource($project->slackIntegrations->getIterator(), 'id');

		$dataGrid->setDataSource($dataSource);

		$cb = static function (\Pd\Monitoring\Slack\Integration $integration): string
		{
			return $integration->getFullName();
		};
		$dataGrid
			->addColumnText('integration', 'Integrace na Slack')
			->setRenderer($cb)
		;

		$cb = static function (\Pd\Monitoring\Slack\Integration $integration): int
		{
			return $integration->id;
		};
		$alreadyIntegrations = \array_map($cb, $dataSource->getData());

		$inlineAdd = $dataGrid->addInlineAdd();
		$addCallback = function (\Nette\Forms\Container $container) use ($alreadyIntegrations): void
		{
			$integrations = $this->integrationRepository->findBy(['id!=' => $alreadyIntegrations])->fetchPairs('id', NULL);
			$integrations = \array_map(static function (\Pd\Monitoring\Slack\Integration $integration): string {
				return $integration->getFullName();
			}, $integrations);

			$container
				->addSelect('integration', '', $integrations)
				->setRequired()
			;
		};
		$inlineAdd->setPositionTop()->onControlAdd[] = $addCallback;

		$inlineAdd->onSubmit[] = function ($values) use ($project): void
		{
			$integration = $this->integrationRepository->getById($values->integration);
			$project->slackIntegrations->add($integration);
			$this->projectsRepository->persistAndFlush($project);
		};

		$cb = function (string $id) use ($project): void
		{
			$integration = $this->integrationRepository->getById((int) $id);
			$project->slackIntegrations->remove($integration);
			$this->projectsRepository->persistAndFlush($project);
		};
		$confirmation = new \Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation('Opravdu?');
		$dataGrid->addActionCallback('delete', 'Odebrat', $cb)->setConfirmation($confirmation);

		return $dataGrid;
	}

}
