<?php

namespace Pd\Monitoring\Check;

use Nextras;


/**
 * @method Check getById(int $id)
 * @method Check getBy(array $conds)
 * @method Nextras\Orm\Collection\ICollection|Check[] findAll()
 */
class ChecksRepository extends Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames()
	{
		return [
			Check::class,
			AliveCheck::class,
			TermCheck::class,
			DnsCheck::class,
			CertificateCheck::class,
			FeedCheck::class,
			RabbitConsumerCheck::class,
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
				case ICheck::TYPE_DNS:
					return DnsCheck::class;
				case ICheck::TYPE_CERTIFICATE:
					return CertificateCheck::class;
				case ICheck::TYPE_FEED:
					return FeedCheck::class;
				case ICheck::TYPE_RABBIT_CONSUMERS:
					return RabbitConsumerCheck::class;

				default:
					throw new \Nextras\Orm\InvalidStateException();
			}
		}
	}
}
