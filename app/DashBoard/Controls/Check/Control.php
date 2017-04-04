<?php declare(strict_types = 1);

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
	 * @var \Kdyby\RabbitMq\Connection
	 */
	private $rabbitConnection;

	/**
	 * @var array|IOnRedraw
	 */
	private $onRedrawListeners = [];


	public function __construct(
		\Pd\Monitoring\Check\Check $check,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\RabbitMq\Connection $rabbitConnection
	) {
		parent::__construct();
		$this->check = $check;
		$this->checksRepository = $checksRepository;
		$this->rabbitConnection = $rabbitConnection;
	}


	public function addOnRedraw(IOnRedraw $onRedraw)
	{
		$this->onRedrawListeners[] = $onRedraw;
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

		$this->processRequest();
	}


	public function handleRefresh()
	{
		$this->rabbitConnection->getProducer($this->check->getProducerName())->publish($this->check->id);

		$this->processRequest();
	}

	public function handleRedraw()
	{
		$this->processRequest();
	}


	private function processRequest()
	{
		if ($this->getPresenter()->isAjax()) {
			$this->redrawControl();
			foreach($this->onRedrawListeners as $listener) {
				$listener->onRedraw($this, $this->check);
			}
		} else {
			$this->redirect('this');
		}
	}


}
