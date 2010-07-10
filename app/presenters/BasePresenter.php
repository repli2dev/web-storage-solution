<?php
/**
 * Base presenter should be inherited by all presenters, sets template and load config into template.
 */
abstract class BasePresenter extends Presenter {

	public $oldLayoutMode = FALSE;

	public function startup(){
		parent::startup();
		$this->getTemplate()->web = Environment::getConfig("web");
		$this->getTemplate()->variable = Environment::getConfig("variable");
	}

	/**
	 * Check if user can see this item (used in menu)
	 * @return bool
	 */
	public function canSee($resource){
		$user = Environment::getUser();
		if(!Environment::getUser()->isAllowed($resource)){
			return FALSE;
		} else {
			return TRUE;
		}
	}
}
