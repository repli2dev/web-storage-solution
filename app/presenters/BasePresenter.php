<?php
namespace App\Presenters;

use Nette\Application\UI\Presenter;

/**
 * Base presenter should be inherited by all presenters, sets template and load config into template.
 */
abstract class BasePresenter extends Presenter
{

	public $oldLayoutMode = FALSE;

	public function startup()
	{
		parent::startup();
		$this->template->web = $this->context->parameters['web'];
		$this->template->variable = $this->context->parameters['variable'];
	}

	/**
	 * Check if user can see this item (used in menu)
	 * @return bool
	 */
	public function canSee($resource)
	{

		if (!$this->getUser()->isAllowed($resource)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}
