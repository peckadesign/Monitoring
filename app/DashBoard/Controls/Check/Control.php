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


	public function __construct(
		\Pd\Monitoring\Check\Check $check,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		parent::__construct();
		$this->check = $check;
		$this->checksRepository = $checksRepository;
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

}
