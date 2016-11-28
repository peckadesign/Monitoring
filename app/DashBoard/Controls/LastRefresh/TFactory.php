<?php

namespace Pd\Monitoring\DashBoard\Controls\LastRefresh;

trait TFactory
{

	/**
	 * @var IFactory
	 */
	private $lastRefreshControlFactory;


	public function injectLastRefreshControlFactory(IFactory $factory)
	{
		$this->lastRefreshControlFactory = $factory;
	}


	protected function createComponentLastRefresh(): Control
	{
		return $this->lastRefreshControlFactory->create();
	}
}
