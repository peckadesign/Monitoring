<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Forms;

class Bs3FormRenderer extends \Nextras\FormsRendering\Renderers\Bs3FormRenderer
{

	public function renderControl(\Nette\Forms\IControl $control): \Nette\Utils\Html
	{
		$html = parent::renderControl($control);

		if ($control instanceof \Pd\Monitoring\DashBoard\Forms\Controls\TextInput) {
			$this->textInput($control, $html);
		}

		return $html;
	}


	private function textInput(\Nette\Forms\IControl $control, $html): void
	{
		if ($control->getLeftOption() || $control->getRightOption()) {
			$container = \Nette\Utils\Html::el('div');
			$container->setAttribute('class', 'input-group');
			if ($control->getLeftOption()) {
				$this->addOption($container, $control->getLeftOption());
			}

			$controlHtml = $html->getChildren();
			$controlHtml = \reset($controlHtml);
			$container->addHtml($controlHtml);

			if ($control->getRightOption()) {
				$this->addOption($container, $control->getRightOption());
			}

			$html->setHtml($container);
		}
	}


	private function addOption(\Nette\Utils\Html $container, string $option): void
	{
		$el = \Nette\Utils\Html::el('span');
		$el->setAttribute('class', 'input-group-addon');
		$el->setText($option);
		$container->addHtml($el);
	}

}
