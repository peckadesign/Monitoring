<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms\Controls;

class DomainControl extends \Nette\Forms\Controls\TextInput
{

	public function __construct(
		$label = NULL, $maxLength = NULL
	) {
		parent::__construct($label, $maxLength);

		$this->setRequired(TRUE);

		$this->setAttribute('placeholder', 'example.com');

		$validator = function (DomainControl $control) {
			return $this->validateDomain($control->getValue());
		};
		$this->addRule($validator, 'URL je nutné zadat ve tvaru example.com');
	}


	private function validateDomain(string $value): bool
	{
		$alpha = "a-z\x80-\xFF";

		return (bool) \preg_match("(^
				(([-_0-9$alpha]+\\.)* # subdomain
				[0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)? # domain
				[$alpha]([-0-9$alpha]{0,17}[$alpha])? # top domain
				\\z)ix", $value);
	}

}
