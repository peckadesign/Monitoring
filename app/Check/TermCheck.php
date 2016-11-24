<?php

namespace Pd\Monitoring\Check;

/**
 * @property string $message
 * @property \DateTime $term
 */
class TermCheck extends Check
{
	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_TERM;
	}


	public function getTitle() : string
	{
		return 'Upozornění na termín';
	}

}
