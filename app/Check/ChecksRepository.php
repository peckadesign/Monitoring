<?php

namespace Pd\Monitoring\Check;

use Nextras;


/**
 * @method User getById(int $id)
 * @method User getBy(array $conds)
 */
class ChecksRepository extends Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames()
	{
		return [
			Check::class,
			AliveCheck::class,
			TermCheck::class,
		];
	}

	public function getEntityClassName(array $data)
	{
		if ( ! isset($data['type'])) {
			return parent::getEntityClassName($data);
		} else {
			switch ($data['type']) {
				case ICheck::TYPE_ALIVE:
					return AliveCheck::class;
				case ICheck::TYPE_TERM:
					return TermCheck::class;

				default:
					throw new \Nextras\Orm\InvalidStateException();
			}
		}
	}
}
