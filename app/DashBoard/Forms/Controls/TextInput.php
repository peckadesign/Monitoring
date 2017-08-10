<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms\Controls;

class TextInput extends \Nette\Forms\Controls\TextInput
{

	/** @var string|null */
	private $leftOption;

	/** @var string|null */
	private $rightOption;


	public function __construct(
		string $label = NULL,
		int $maxLength = NULL,
		string $leftOption = NULL,
		string $rightOption = NULL
	) {
		parent::__construct($label, $maxLength);

		$this->leftOption = $leftOption;
		$this->rightOption = $rightOption;
	}


	public function getLeftOption(): ?string
	{
		return $this->leftOption;
	}


	public function getRightOption(): ?string
	{
		return $this->rightOption;
	}

}
