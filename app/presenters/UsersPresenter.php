<?php

/**
 * Presenter to add/edit/remove users
 *
 * @author     Jan Drabek
 * @package    Own storage web
 */
class UsersPresenter extends BasePresenter {
	public function actionLogin() {
		if($this->canSee("files")){
			$this->redirect("Files:default");
		}

	}
	public function actionLogout() {
		Environment::getUser()->logout(TRUE);
		$this->redirect("login");
	}
	public function actionDefault() {
		if(!$this->canSee("users")){
			$this->redirect("Users:login");
		} else {
			$model = new UsersModel();
			$data = $model->findAll();
			$this->getTemplate()->data = $data;
		}
	}
	public function actionAdd() {
		if(!$this->canSee("users")){
			$this->redirect("Users:login");
		}
	}

	public function actionEdit($id) {
		if(!$this->canSee("users")){
			$this->redirect("Users:login");
		}
	}

	public function actionRemove($id) {
		if(!$this->canSee("users")){
			$this->redirect("Users:login");
		} else {
			$model = new UsersModel();
			$data = $model->find($id);
			$this->getTemplate()->data = $data;
		}
	}

	public function actionPassword() {
		if(!$this->canSee("users") && !$this->canSee("files")){
			$this->redirect("Users:login");
		}
		
	}

	// SIGNALS and HANDLERS
	public function loginFormSubmitted(Form $form) {
		$values = $form->getValues();
		try {
			// Try to authenticate
			Environment::getUser()->login($values['username'],$values['password']);
			$this->redirect("Files:default");
		} catch (AuthenticationException $e) {
			// If any exception was cought than show proper error message
			switch ($e->getCode()) {
				case IAuthenticator::IDENTITY_NOT_FOUND:
					$form->addError("User do not exists.");
					break;
				case IAuthenticator::INVALID_CREDENTIAL:
					$form->addError("Password is wrong.");
					break;
			}
		}
	}

	public function addFormSubmitted(Form $form) {
		$values = $form->getValues();
		unset($values["again"]);
		$values["password"] = sha1($values["password"]);

		$model = new UsersModel();
		$model->add($values);
		// Create message and redirect back
		$this->flashMessage('User was successfully added.','success');
		$this->redirect("default");
	}

	public function editFormSubmitted(Form $form) {
		$id = $this->getParam('id');
		$values = $form->getValues();
		unset($values["again"]);
		if(!empty($values["password"])) {
			$values["password"] = sha1($values["password"]);
		} else {
			unset($values["password"]);
		}

		$model = new UsersModel();
		$model->edit($id,$values);
		// Create message and redirect back
		$this->flashMessage('User was successfully edited.','success');
		$this->redirect("default");
	}

	public function deleteFormSubmitted(Form $form){
		// Check if form was submitted by yes button
		if($form["yes"]->isSubmittedBy()){
			// Get id of item to delete
			$id = $this->getParam('id');
			// Delete item
			$model = new UsersModel();
			$model2 = new FilesModel();
			$allFiles = $model2->findByUser($id);
			if(count($allFiles) > 0){
				foreach($allFiles as $file){
					if(file_exists('./files/'.$file->hash)){
						unlink('./files/'.$file->hash);
						$model2->delete($file->id);
					}
				}
			}
			$model->delete($id);
			// Create message and redirect back
			$this->flashMessage('User was successfully deleted.','success');
			$this->redirect('default');
		} else {
			$this->redirect('default');
		}
	}

	public function changeFormSubmitted(Form $form){
		$id = Environment::getUser()->getIdentity()->id;
		$values = $form->getValues();

		unset($values["again"]);
		$values["password"] = sha1($values["password"]);

		$model = new UsersModel();
		$model->edit($id, $values);
		$this->flashMessage('Your password was successfully changed.','success');
		$this->redirect('this');
	}

	// PROTECTED

	protected function createComponent($name) {
		switch($name) {
			case 'loginForm':
				$form = new BaseForm($this,$name);
				$form->addText('username','Username:')
					->addRule(Form::FILLED,'Please fill username.')
					->getControlPrototype()
						->setClass("text");
				$form->addPassword('password','Password:')
					->addRule(Form::FILLED,'Please fill password.')
					->getControlPrototype()
						->setClass("text");
				$form->addSubmit('submitted','Login');
				$form->onSubmit[] = array($this,'loginFormSubmitted');
				return $form;
				break;
			case 'addForm':
				$form = $this->prepareForm();

				$form->onSubmit[] = array($this,'addFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'editForm':
				$form = $this->prepareForm('edit');

				$id = $this->getParam('id');
				if($id == 1){
					throw new BadRequestException();
				}
				$model = new UsersModel();
				$values = $model->find($id);
				$form->setDefaults($values);

				$form->onSubmit[] = array($this,'editFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'removeForm':
				$id = $this->getParam('id');
				if($id == 1){
					throw new BadRequestException();
				}
				$form = new BaseForm;
				$form->getRenderer()->wrappers['controls']['container'] = NULL;
				$form->confirmAndProcess($this, 'deleteFormSubmitted');
				return $form;
				break;
			case 'changeForm':
				$form = new  BaseForm;
				$password = $form->addPassword("password","Password:");
				$password->getControlPrototype()
					->class('text');
				$password->addRule(Form::FILLED,"Fill the password.");
				$again = $form->addPassword("again","Password again:");
				$again->addRule(Form::FILLED,"Fill the second password for check.");
				$again->addCondition(Form::FILLED,$form["again"])
					->addRule(Form::EQUAL,"Passwords has to match.", $form["password"]);

				$again->getControlPrototype()
					->class('text');
				$form->addSubmit("submitted", "Change");
				$form->onSubmit[] = array($this, 'changeFormSubmitted');
				return $form;
				break;
			default:
				break;
		}
	}

	protected function prepareForm($owner = "add"){
		$form = new BaseForm;
		$form->addText("username","Username:")
			->addRule(Form::FILLED,"Fill the username.")
			->getControlPrototype()
				->class('text');
		$password = $form->addPassword("password","Password:");
		$password->getControlPrototype()
				->class('text');
		if($owner == "add") {
			$password->addRule(Form::FILLED,"Fill the password.");
		}

		$again = $form->addPassword("again","Password again:");
		if($owner == "add") {
			$again->addRule(Form::FILLED,"Fill the second password for check.");
			$again->addCondition(Form::FILLED,$form["again"])
				->addRule(Form::EQUAL,"Passwords has to match.", $form["password"]);
		}
		$again->getControlPrototype()
				->class('text');

		if($owner == "edit") {
			$password->addConditionOn($again, Form::FILLED)
					->addRule(Form::FILLED,"Fill the password");
			$again->addConditionOn($password, Form::FILLED)
					->addRule(Form::FILLED,"Fill the second password for check.");
			$again->addCondition(Form::FILLED,$form["again"])
				->addRule(Form::EQUAL,"Passwords has to match.", $form["password"]);
		}
		
		$form->addSelect('role','Role',array("normal" => "Normal", "master" => "Master"))
			->addRule(Form::FILLED,"Choose role.");
		if($owner == "edit"){
			$form->addSubmit("submitted", "Edit");
		} else {
			$form->addSubmit("submitted", "Add");
		}
		return $form;
	}
}
