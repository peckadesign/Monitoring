<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

final class SlackPresenter extends BasePresenter
{

	private \Pd\Monitoring\DashBoard\Controls\SlackIntegrationList\IFactory $factory;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\SlackIntegrationList\IFactory $factory
	)
	{
		parent::__construct();
		$this->factory = $factory;
	}


	public function actionDefault()
	{
	}


	public function actionEdit(\Pd\Monitoring\Slack\Integration $integration): void
	{

	}


	public function renderEdit(\Pd\Monitoring\Slack\Integration $integration): void
	{
		$this->template->name = $integration->getFullName();
	}


	public function createComponentSlackIntegrationList(): \Pd\Monitoring\DashBoard\Controls\SlackIntegrationList\Control
	{
		return $this->factory->create();
	}

}
