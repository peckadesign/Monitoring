<?php declare(strict_types = 1);

namespace Pd\Monitoring\Presenters;

class ErrorPresenter implements \Nette\Application\IPresenter
{

	use \Nette\SmartObject;


	private \Monolog\Logger $logger;


	public function __construct(
		\Monolog\Logger $logger
	)
	{
		$this->logger = $logger;
	}


	public function run(\Nette\Application\Request $request): \Nette\Application\IResponse
	{
		$e = $request->getParameter('exception');

		if ($e instanceof \Nette\Application\BadRequestException) {
			[$module, , $sep] = \Nette\Application\Helpers::splitName($request->getParameter('request')->getPresenterName());

			return new \Nette\Application\Responses\ForwardResponse($request->setPresenterName($module . $sep . 'Error4xx'));
		}

		$this->logger->error($e);

		return new \Nette\Application\Responses\CallbackResponse(static function ()
		{
			require __DIR__ . '/templates/Error/500.phtml';
		});
	}

}
