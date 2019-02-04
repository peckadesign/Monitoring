<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string $id {primary-proxy}
 * @property \Pd\Monitoring\Project\Project $project {m:1 \Pd\Monitoring\Project\Project::$checks} {primary}
 * @property int $type {enum ICheck::TYPE_*} {primary}
 */
final class CheckTypesInProject extends \Nextras\Orm\Entity\Entity
{

}
