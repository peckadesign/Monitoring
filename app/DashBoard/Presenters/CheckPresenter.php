<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

final class CheckPresenter extends BasePresenter
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\LogView\Factory
	 */
	private $logViewFactory;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	private $check;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\LogView\Factory $logViewFactory,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository
	) {
		parent::__construct();
		$this->logViewFactory = $logViewFactory;
		$this->checksRepository = $checksRepository;
	}


	/**
	 * @Acl(check, edit)
	 */
	public function actionLogView(int $id): void
	{
		$this->check = $this->checksRepository->getById($id);
		if ( ! $this->check) {
			$this->error();
		}
	}


	public function renderLogView(): void
	{
		$this
			->getTemplate()
			->add('check', $this->check)
		;
	}


	protected function createComponentLogView(): \Nette\Application\UI\Control
	{
		return $this->logViewFactory->create($this->check);
	}

}
