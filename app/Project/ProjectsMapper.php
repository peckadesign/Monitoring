<?php declare(strict_types = 1);

namespace Pd\Monitoring\Project;

class ProjectsMapper extends \Nextras\Orm\Mapper\Mapper
{

	/**
	 * @param array<int> $userFavoriteProjectsIds
	 * @param array<int> $onlyProjectsIds
	 */
	public function findDashBoardProjects(array $userFavoriteProjectsIds, array $onlyProjectsIds)
	{
		$data = $this
			->builder()
			->where('%column IS NULL', 'parent')
			->andWhere('%column = 0', 'reference')
			->orderBy('name')
		;

		if ($userFavoriteProjectsIds !== []) {
			$data->andWhere('%column NOT IN %i[]', 'id', $userFavoriteProjectsIds);
		}

		if ($onlyProjectsIds !== []) {
			$data->andWhere('%column IN %i[]', 'id', $onlyProjectsIds);
		} else {
			$data->andWhere('%column IS NULL', 'id');
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
