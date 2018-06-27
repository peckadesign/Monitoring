<?php declare(strict_types = 1);

namespace Pd\Monitoring\Project;

class ProjectsMapper extends \Nextras\Orm\Mapper\Mapper
{

	public function findDashBoardProjects(array $userFavoriteProjectsIds)
	{
		$data = $this
			->builder()
			->where('%column IS NULL', 'parent')
			->andWhere('%column = 0', 'reference')
			->orderBy('name')
		;

		if ($userFavoriteProjectsIds) {
			$data->andWhere('%column NOT IN %i[]', 'id', $userFavoriteProjectsIds);
		}

		return $this->toCollection($data);
	}


	public function findParentAbleProjects(?Project $without = NULL)
	{
		$data = $this
			->builder()
			->where('%column IS NULL', 'parent')
			->orderBy('name')
		;

		if ($without) {
			$data->andWhere('%column != %i', 'id', $without->id);
		}

		return $this->toCollection($data);
	}

}
