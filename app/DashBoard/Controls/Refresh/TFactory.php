<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Refresh;

trait TFactory
{

	private IFactory $refreshControlFactory;


	public function injectRefreshControlFactory(IFactory $factory): void
	{
		$this->refreshControlFactory = $factory;
	}


	protected function createComponentRefresh(): Control
	{
		return $this->refreshControlFactory->create();
	}

}
