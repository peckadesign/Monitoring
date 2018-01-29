<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\LastRefresh;

trait TFactory
{

	/**
	 * @var IFactory
	 */
	private $lastRefreshControlFactory;


	public function injectLastRefreshControlFactory(IFactory $factory): void
	{
		$this->lastRefreshControlFactory = $factory;
	}


	protected function createComponentLastRefresh(): Control
	{
		return $this->lastRefreshControlFactory->create();
	}
}
