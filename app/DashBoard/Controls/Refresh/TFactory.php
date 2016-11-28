<?php

namespace Pd\Monitoring\DashBoard\Controls\Refresh;

trait TFactory
{

	/**
	 * @var IFactory
	 */
	private $refreshControlFactory;


	public function injectRefreshControlFactory(IFactory $factory)
	{
		$this->refreshControlFactory = $factory;
	}


	protected function createComponentRefresh(): Control
	{
		return $this->refreshControlFactory->create();
	}
}
