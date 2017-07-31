<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Settings;

trait TFactory
{

	/**
	 * @var IFactory
	 */
	private $settingsControlFactory;


	public function injectSettingsControlFactory(IFactory $factory)
	{
		$this->settingsControlFactory = $factory;
	}


	protected function createComponentSettings(): Control
	{
		return $this->settingsControlFactory->create();
	}

}
