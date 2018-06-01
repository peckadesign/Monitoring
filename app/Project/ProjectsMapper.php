<?php declare(strict_types = 1);

namespace Pd\Monitoring\Project;

class ProjectsMapper extends \Nextras\Orm\Mapper\Mapper
{

	public function findDashBoardProjects(array $userFavoriteProjectsIds)
	{
		$data = $this
			->builder()
			->where('%column NOT IN %i[]', 'id', $userFavoriteProjectsIds)
			->andWhere('%column IS NULL', 'parent')
			->orderBy('name')
		;

		return $this->toCollection($data);
	}


	public function findParentAbleProjects()
	{
		$data = $this
			->builder()
			->where('%column IS NULL', 'parent')
			->orderBy('name')
		;

		return $this->toCollection($data);
	}

}
