<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms\Controls;

final class UrlControlFactory
{

	public static function create(): \Nette\Forms\Controls\TextInput
	{
		$control = new \Nette\Forms\Controls\TextInput('URL');

		$control->setRequired(TRUE);
		$control->setAttribute('placeholder', 'https://www.example.com');
		$control->setOption('description', 'URL musí vracet HTTP stavový kód 200.');
		$control->addRule(\Nette\Forms\Form::URL);

		return $control;
	}

}
