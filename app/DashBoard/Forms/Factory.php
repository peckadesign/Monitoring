<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms;

class Factory
{

	public function create() : \Nette\Application\UI\Form
	{
		$form = new \Nette\Application\UI\Form();

		$form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());

		return $form;
	}
}
