<?php

namespace Pd\Monitoring\DashBoard\Presenters;

trait TSecuredPresenter
{

	public function checkRequirements($element)
	{
		if ( ! $this->user->loggedIn) {
			$this->redirect(':DashBoard:Login:default', ['backLink' => $this->storeRequest()]);
		}
	}

}
