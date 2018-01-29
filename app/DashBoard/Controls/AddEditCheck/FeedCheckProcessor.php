<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class FeedCheckProcessor implements ICheckControlProcessor
{

	public function processEntity(\Pd\Monitoring\Check\Check $check, array $data): void
	{
		$check->maximumAge = $data['maximumAge'];
		$check->size = $data['size'];
		$check->url = $data['url'];
	}


	public function getCheck(): \Pd\Monitoring\Check\Check
	{
		return new \Pd\Monitoring\Check\FeedCheck();
	}


	public function createForm(\Pd\Monitoring\Check\Check $check, \Nette\Application\UI\Form $form): void
	{
		$form->addGroup($check->getTitle());
		$form
			->addText('url', 'Adresa')
			->setRequired(TRUE)
			->setAttribute('placeholder', 'https://www.example.com/feed.xml')
			->addRule(\Nette\Forms\Form::URL)
		;

		$form['size'] = new \Pd\Monitoring\DashBoard\Forms\Controls\TextInput('Minimální velikost', NULL, NULL, 'MB');
		$form['size']->setRequired(TRUE);
		$form['size']->addRule(\Nette\Forms\Form::FLOAT);
		$form['size']->setAttribute('placeholder', '12.5');

		$form['maximumAge'] = new \Pd\Monitoring\DashBoard\Forms\Controls\TextInput('Maximální stáří feedu', NULL, NULL, 'hodin');
		$form['maximumAge']->setRequired(TRUE);
		$form['maximumAge']->addRule(\Nette\Forms\Form::INTEGER);
		$form['maximumAge']->setType('number');
	}

}
