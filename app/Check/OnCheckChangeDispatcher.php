<?php

namespace Pd\Monitoring\Check;

class OnCheckChangeDispatcher implements IOnCheckChangeDispatcher
{

	/**
	 * @var iterable|IOnCheckChange[]
	 */
	private $listeners;


	public function change(Check $check): void
	{
		foreach ($this->listeners as $listener) {
			$listener->onChange($check);
		}
	}


	public function addListener(IOnCheckChange $onCheckChange): void
	{
		$this->listeners[] = $onCheckChange;
	}
}
