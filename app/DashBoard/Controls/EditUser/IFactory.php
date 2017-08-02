<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\EditUser;

interface IFactory
{

	public function create(\Pd\Monitoring\User\User $identity): Control;

}
