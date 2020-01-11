<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @property string|NULL $errorsJson
 * @property string|NULL $mutedErrorsJson
 * @property-read array|NULL $errors {virtual}
 * @property-read array|NULL $mutedErrors {virtual}
 * @property-read array|NULL $notifyErrors {virtual}
 */
class ErrorsCheck extends Check
{


	public function __construct()
	{
		parent::__construct();
		$this->type = ICheck::TYPE_ERRORS;
	}


	protected function getterErrors(): ?array
	{
		return $this->errorsJson
			? \Nette\Utils\Json::decode($this->errorsJson, \Nette\Utils\Json::FORCE_ARRAY)
			: NULL
		;
	}


	protected function getterMutedErrors(): array
	{
		return $this->mutedErrorsJson
			? \Nette\Utils\Json::decode($this->mutedErrorsJson, \Nette\Utils\Json::FORCE_ARRAY)
			: []
		;
	}


	protected function getterNotifyErrors(): ?array
	{
		$errors = $this->errors;
		$mutedErrors = $this->mutedErrors;

		if ($errors === NULL) {
			return NULL;
		}

		if (\count($mutedErrors) === 0) {
			return $errors;
		}

		return \array_diff($errors, $mutedErrors);
	}


	public function getStatus(): int
	{
		$notifyErrors = $this->notifyErrors;

		if ($notifyErrors === NULL) {
			return ICheck::STATUS_ERROR;
		}

		return \count($notifyErrors) === 0
			? ICheck::STATUS_OK
			: ICheck::STATUS_ALERT
		;
	}


	public function getTitle(): string
	{
		return 'Počet chyb';
	}


	public function getterStatusMessage(): string
	{
		$notifyErrors = $this->notifyErrors;

		if ($notifyErrors === NULL) {
			return 'nepodařilo se zjistit';
		}

		if (\count($notifyErrors) === 0) {
			return 'nejsou';
		}

		return \implode(', ', $notifyErrors);
	}

}
