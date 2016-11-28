<?php

namespace Pd\Monitoring\DashBoard\Controls\Check;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	private $check;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Kdyby\RabbitMq\IProducer
	 */
	private $producer;


	public function __construct(
		\Pd\Monitoring\Check\Check $check,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\RabbitMq\IProducer $producer
	) {
		parent::__construct();
		$this->check = $check;
		$this->checksRepository = $checksRepository;
		$this->producer = $producer;
	}


	protected function createTemplate()
	{
		/** @var \Latte\Runtime\Template $template */
		$template = parent::createTemplate();

		$template->addFilter('dateTime', function (\DateTime $value) {
			return $value->format('j. n. Y H:i:s');
		});

		return $template;
	}


	public function render()
	{
		$this->template->check = $this->check;

		$this->template->setFile(__DIR__ . '/Control.latte');
		$this->template->render();
	}


	public function handleDelete()
	{
		$this->checksRepository->removeAndFlush($this->check);

		$this->redirect('this');
	}


	public function handlePause()
	{
		$this->check->paused = ! $this->check->paused;
		$this->checksRepository->persistAndFlush($this->check);

		$this->redirect('this');
	}


	public function handleRefresh()
	{
		$this->producer->publish($this->check->id);

		$this->redirect('this');
	}

}
