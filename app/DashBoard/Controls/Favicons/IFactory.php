<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Favicons;

interface IFactory
{
	public function create() : Control;
}
