<?php
namespace App\Components;

use Nette\Application\UI\Form;

/**
 * Base form which adds some basic settings, events, and confirm type of form
 */
class BaseForm extends Form {

	public function __construct()
	{
		parent::__construct();
		// Overwrite table in rendering forms
		$renderer = $this->getRenderer();
		$renderer->wrappers['label']['requiredsuffix'] = "<span class=\"red\">&nbsp;*</span>";

		//$this->setTranslator(System::translator());
	}

	/**
	 * Method for creating yes, no dialog.
	 */
	public function confirmAndProcess($on, $method)
	{
		$this->getElementPrototype()->class("confirm-dialog");
		$this->addSubmit("yes", "Ano")
			->getControlPrototype()->class("yes-button");
		$this->addSubmit("no", "Ne")
			->getControlPrototype()->class("no-button");
		$this->onSubmit[] = array($on, $method);
	}

}

// Add DateTime Picker to forms
function Form_addDateTimePicker(Form $_this, $name, $label, $cols = NULL, $maxLength = NULL)
{
	return $_this[$name] = new DateTimePicker($label, $cols, $maxLength);
}

Form::extensionMethod('Nette\\Application\\UI\\Form::addDateTimePicker', function (Form $_this, $name, $label, $maxLength = NULL) {
	return $_this[$name] = new DateTimePicker($label, $maxLength);
});