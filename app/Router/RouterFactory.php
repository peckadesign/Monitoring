<?php declare(strict_types = 1);

namespace Pd\Monitoring\Router;

class RouterFactory
{

	use \Nette\SmartObject;

	/**
	 * @var \Nette\Caching\IStorage
	 */
	private $storage;

	/**
	 * @var \Pd\Monitoring\Project\ProjectsRepository
	 */
	private $projectsRepository;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\User\UsersRepository
	 */
	private $usersRepository;


	public function __construct(
		\Nette\Caching\IStorage $storage,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
	 	\Pd\Monitoring\User\UsersRepository $usersRepository
	) {
		$this->storage = $storage;
		$this->projectsRepository = $projectsRepository;
		$this->checksRepository = $checksRepository;
		$this->usersRepository = $usersRepository;
	}


	public function createRouter(): \Nette\Application\IRouter
	{
		$router = new \Nette\Application\Routers\RouteList();

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'Octocats',
			'action' => 'default',
			NULL => [
				\Nette\Application\Routers\Route::FILTER_OUT => function (array $parameters) {
					if ($parameters['presenter'] === 'Octocats' && $parameters['action'] === 'random') {

						$cache = new \Nette\Caching\Cache($this->storage);

						$fb = static function (&$dp): array {
							$octodexFeedContent = \file_get_contents('https://feeds.feedburner.com/Octocats');
							$octodexFeed = new \SimpleXMLElement($octodexFeedContent);
							$octocats = [];
							foreach ($octodexFeed->entry as $entry) {
								$imageUrl = (string) $entry->content->div->a->img['src'];
								if (\Nette\Utils\Validators::isUrl($imageUrl)) {
									$octocat = \substr($imageUrl, \strrpos($imageUrl, '/') + 1);
									$octocats[] = $octocat;
								}
							}

							$dp[\Nette\Caching\Cache::EXPIRE] = '+24 hours';

							return $octocats;
						};

						$octocats = $cache->load('octocats', $fb);

						\shuffle($octocats);
						$octocat = \reset($octocats);

						$parameters = [];
						$parameters['octocat'] = $octocat;
					}

					return $parameters;
				},
			],
		];
		$router[] = new \Nette\Application\Routers\Route('https://octodex.github.com/images/<octocat [a-z0-9\.\-]+>', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'Project',
			'action' => 'default',
			'project' => [
				\Nette\Application\Routers\Route::FILTER_IN => function(int $project): ?\Pd\Monitoring\Project\Project {
					return $this->projectsRepository->getById($project);
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function(\Pd\Monitoring\Project\Project $project): int {
					return $project->id;
				},
			]
		];
		$router[] = new \Nette\Application\Routers\Route('dash-board/project/<action>[/<project>]', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'Check',
			'action' => 'default',
			'check' => [
				\Nette\Application\Routers\Route::FILTER_IN => function(int $check): ?\Pd\Monitoring\Check\Check {
					return $this->checksRepository->getById($check);
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function(\Pd\Monitoring\Check\Check $check): int {
					return $check->id;
				},
			]
		];
		$router[] = new \Nette\Application\Routers\Route('dash-board/check/<action>[/<check>]', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'User',
			'action' => 'edit',
			'user' => [
				\Nette\Application\Routers\Route::FILTER_IN => function(int $user): ?\Pd\Monitoring\User\User {
					return $this->usersRepository->getById($user);
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function(\Pd\Monitoring\User\User $user): int {
					return $user->id;
				},
			]
		];
		$router[] = new \Nette\Application\Routers\Route('dash-board/user/<action>[/<user>]', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'HomePage',
			'action' => 'default',
		];
		$router[] = new \Nette\Application\Routers\Route('<module>/<presenter>/<action>', $metadata);

		return $router;
	}

}
