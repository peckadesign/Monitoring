<?php

namespace Pd\Monitoring\Project;

/**
 * @property int $id {primary}
 * @property string $name
 * @property string $url
 * @property \DateTime|NULL $maintenance
 * @property string|NULL $pausedFrom
 * @property string|NULL $pausedTo
 * @property \Nextras\Orm\Relationships\OneHasMany|\Pd\Monitoring\Check\Check[] $checks {1:m \Pd\Monitoring\Check\Check::$project}
 */
class Project extends \Nextras\Orm\Entity\Entity
{

}
