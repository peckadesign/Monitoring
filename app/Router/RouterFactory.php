<?php declare(strict_types = 1);

namespace Pd\Monitoring\Router;

class RouterFactory
{

	use \Nette\SmartObject;


	private \Nette\Caching\IStorage $storage;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;

	private \Pd\Monitoring\Check\ChecksRepository $checksRepository;

	private \Pd\Monitoring\User\UsersRepository $usersRepository;


	public function __construct(
		\Nette\Caching\IStorage $storage,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\User\UsersRepository $usersRepository
	)
	{
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
				\Nette\Application\Routers\Route::FILTER_OUT => function (array $parameters)
				{
					if ($parameters['presenter'] === 'Octocats' && $parameters['action'] === 'random') {

						$cache = new \Nette\Caching\Cache($this->storage);

						$fb = static function (&$dp): array
						{
							$octodexFeedContent = \file_get_contents('https://octodex.github.com/atom.xml');
							$octodexFeed = new \SimpleXMLElement($octodexFeedContent);
							$octocats = [];
							foreach ($octodexFeed->entry as $entry) {
								$matched = \preg_match('~src="([^"]+)"~', (string) $entry->content, $matches);
								if ( ! $matched) {
									continue;
								}
								if (\Nette\Utils\Validators::isUrl($matches[1])) {
									$octocats[] = \basename($matches[1]);
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
				\Nette\Application\Routers\Route::FILTER_IN => function (string $project): ?\Pd\Monitoring\Project\Project
				{
					return $this->projectsRepository->getById((int) $project);
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function (\Pd\Monitoring\Project\Project $project): int
				{
					return \count($project->subProjects) ? \current($project->subProjects->getEntitiesForPersistence())->id : $project->id;
				},
			],
		];
		$router[] = new \Nette\Application\Routers\Route('dash-board/project/<action>[/<project>]', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'Check',
			'action' => 'default',
			NULL => [
				\Nette\Application\Routers\Route::FILTER_IN => function (array $parameters): array
				{
					if (isset($parameters['project'])) {
						$parameters['project'] = $this->projectsRepository->getById($parameters['project']);
					}

					return $parameters;
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function (array $parameters): array
				{
					if (isset($parameters['check'])) {
						$parameters['project'] = $parameters['check']->project->id;
						$parameters['type'] = $parameters['check']->type;
						unset($parameters['check']);
					}

					return $parameters;
				},
			],
		];
		$router[] = new \Nette\Application\Routers\Route('dash-board/project/default/<project>', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'Check',
			'action' => 'default',
			'check' => [
				\Nette\Application\Routers\Route::FILTER_IN => function (string $check): ?\Pd\Monitoring\Check\Check
				{
					return $this->checksRepository->getById((int) $check);
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function (\Pd\Monitoring\Check\Check $check): int
				{
					return $check->id;
				},
			],
		];
		$router[] = new \Nette\Application\Routers\Route('dash-board/check/<action>[/<check>]', $metadata);

		$metadata = [
			'module' => 'DashBoard',
			'presenter' => 'User',
			'action' => 'edit',
			'user' => [
				\Nette\Application\Routers\Route::FILTER_IN => function (string $user): ?\Pd\Monitoring\User\User
				{
					return $this->usersRepository->getById((int) $user);
				},
				\Nette\Application\Routers\Route::FILTER_OUT => static function (\Pd\Monitoring\User\User $user): int
				{
					return $user->id;
				},
			],
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
