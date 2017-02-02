<?php

namespace Pd\Monitoring\Check;

/**
 * @property int $id {primary}
 * @property Check $check {m:1 Check::$locks}
 * @property int $status {enum ICheck::STATUS_*}
 * @property \DateTime $locked
 */
class SlackNotifyLock extends \Nextras\Orm\Entity\Entity
{

}
