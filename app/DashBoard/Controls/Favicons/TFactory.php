<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Favicons;

trait TFactory
{

	/**
	 * @var IFactory
	 */
	private $faviconsControlFactory;


	public function injectFaviconsControlFactory(IFactory $factory): void
	{
		$this->faviconsControlFactory = $factory;
	}


	protected function createComponentFavicons(): \Nette\Application\UI\Control
	{
		return $this->faviconsControlFactory->create();
	}
}
