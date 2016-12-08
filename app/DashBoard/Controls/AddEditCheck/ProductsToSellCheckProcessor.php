<?php

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class ProductsToSellCheckProcessor implements \Pd\Monitoring\DashBoard\Controls\AddEditCheck\ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data)
	{
		$check->url = $data['url'];
		$check->count = $data['count'];
	}


	public function getCheck() : \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\ProductsToSellCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form)
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'URL')
			->setRequired(TRUE)
		;
		$form
			->addText('count', 'Minimální počet produktů')
			->setRequired(TRUE)
		;
	}
}
