<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\LastRefresh;

trait TFactory
{

	private IFactory $lastRefreshControlFactory;


	public function injectLastRefreshControlFactory(IFactory $factory): void
	{
		$this->lastRefreshControlFactory = $factory;
	}


	protected function createComponentLastRefresh(): Control
	{
		return $this->lastRefreshControlFactory->create();
	}

}
