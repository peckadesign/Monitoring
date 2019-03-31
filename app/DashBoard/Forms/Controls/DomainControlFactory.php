<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms\Controls;

final class DomainControlFactory
{

	public static function create(): \Nette\Forms\Controls\TextInput
	{
		$control = new \Nette\Forms\Controls\TextInput('Doména');
		$control->setRequired(TRUE);
		$control->setAttribute('placeholder', 'example.com');
		$control->setOption('description', 'Např. "example.com".');

		$validator = function (\Nette\Forms\Controls\TextInput $control) {
			return self::validateDomain($control->getValue());
		};
		$control->addRule($validator, 'URL je nutné zadat ve tvaru example.com');

		return $control;
	}


	public static function validateDomain(string $value): bool
	{
		$alpha = "a-z\x80-\xFF";

		return (bool) \preg_match("(^
				(([-_0-9$alpha]+\\.)* # subdomain
				[0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)? # domain
				[$alpha]([-0-9$alpha]{0,17}[$alpha])? # top domain
				\\z)ix", $value);
	}

}
