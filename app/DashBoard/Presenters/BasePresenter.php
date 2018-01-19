<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

use Nette;
use Pd;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	use TSecuredPresenter;
	use Pd\Monitoring\DashBoard\Controls\LastRefresh\TFactory;
	use Pd\Monitoring\DashBoard\Controls\Settings\TFactory;
	use Pd\Monitoring\DashBoard\Controls\Favicons\TFactory;

	public const FLASH_MESSAGE_SUCCESS = 'success';
	public const FLASH_MESSAGE_INFO = 'info';
	public const FLASH_MESSAGE_WARNING = 'warning';
	public const FLASH_MESSAGE_ERROR = 'danger';

	/**
	 * @var Pd\Monitoring\DashBoard\Controls\Logout\IFactory
	 */
	private $logoutControlFactory;

	/**
	 * @var Pd\Monitoring\User\UsersRepository
	 */
	private $usersRepository;


	public function injectServices(
		Pd\Monitoring\DashBoard\Controls\Logout\IFactory $logoutControlFactory,
		Pd\Monitoring\User\UsersRepository $usersRepository
	) {
		$this->logoutControlFactory = $logoutControlFactory;
		$this->usersRepository = $usersRepository;
	}


	protected function createComponentLogout() : Pd\Monitoring\DashBoard\Controls\Logout\Control
	{
		return $this->logoutControlFactory->create();
	}


	protected function createTemplate()
	{
		$template = parent::createTemplate();

		$template->addFilter('dateTime', function (\DateTime $s) {
			return $s->format('j. n. Y H:i:s');
		});

		return $template;
	}


	public function flashMessage($message, $type = 'info')
	{
		if ($this->isAjax()) {
			$this->redrawControl('flashMessages');
		}

		return parent::flashMessage($message, $type);
	}

}
