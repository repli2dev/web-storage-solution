<?php
namespace App\Presenters;

use App\Components\BaseForm;
use App\Model\Files;
use App\Model\Users;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;

class UsersPresenter extends BasePresenter
{
	/** @var Users */
	private $users;
	/** @var Files */
	private $files;

	public function injectUsersAndFiles(Users $users, Files $files)
	{
		$this->users = $users;
		$this->files = $files;
	}

	public function actionLogin()
	{
		if ($this->canSee("files")) {
			$this->redirect("Files:default");
		}

	}

	public function actionLogout()
	{
		$this->user->logout(TRUE);
		$this->redirect("login");
	}

	public function actionDefault()
	{
		if (!$this->canSee("users")) {
			$this->redirect("Users:login");
		} else {
			$data = $this->users->findAll();
			$this->getTemplate()->data = $data;
		}
	}

	public function actionAdd()
	{
		if (!$this->canSee("users")) {
			$this->redirect("Users:login");
		}
	}

	public function actionEdit($id)
	{
		if (!$this->canSee("users")) {
			$this->redirect("Users:login");
		}
	}

	public function actionRemove($id)
	{
		if (!$this->canSee("users")) {
			$this->redirect("Users:login");
		} else {
			$data = $this->users->find($id);
			$this->getTemplate()->data = $data;
		}
	}

	public function actionPassword()
	{
		if (!$this->canSee("users") && !$this->canSee("files")) {
			$this->redirect("Users:login");
		}

	}

	// SIGNALS and HANDLERS
	public function loginFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		try {
			// Try to authenticate
			$this->user->login($values['username'], $values['password']);
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

	public function addFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		unset($values["again"]);
		$values["password"] = sha1($values["password"]);

		$this->users->add($values);
		// Create message and redirect back
		$this->flashMessage('User was successfully added.', 'success');
		$this->redirect("default");
	}

	public function editFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		$id = $this->getParameter('id');
		unset($values["again"]);
		if (!empty($values["password"])) {
			$values["password"] = sha1($values["password"]);
		} else {
			unset($values["password"]);
		}

		$this->users->edit($id, $values);
		// Create message and redirect back
		$this->flashMessage('User was successfully edited.', 'success');
		$this->redirect("default");
	}

	public function deleteFormSubmitted(Form $form)
	{
		// Check if form was submitted by yes button
		if ($form["yes"]->isSubmittedBy()) {
			// Get id of item to delete
			$id = $this->getParameter('id');
			// Delete item
			$model = $this->users;
			$model2 = $this->files;
			$allFiles = $model2->findByUser($id);
			if (count($allFiles) > 0) {
				foreach ($allFiles as $file) {
					if (file_exists('./files/' . $file->hash)) {
						unlink('./files/' . $file->hash);
						$model2->delete($file->id);
					}
				}
			}
			$model->delete($id);
			// Create message and redirect back
			$this->flashMessage('User was successfully deleted.', 'success');
			$this->redirect('default');
		} else {
			$this->redirect('default');
		}
	}

	public function changeFormSubmitted(Form $form)
	{
		$id = $this->getUser()->getId();
		$values = $form->getValues();

		unset($values["again"]);
		$values["password"] = sha1($values["password"]);

		$this->users->edit($id, $values);
		$this->flashMessage('Your password was successfully changed.', 'success');
		$this->redirect('this');
	}

	// PROTECTED

	protected function createComponent($name)
	{
		switch ($name) {
			case 'loginForm':
				$form = new BaseForm($this, $name);
				$form->addText('username', 'Username:')
					->addRule(Form::FILLED, 'Please fill username.')
					->getControlPrototype()
					->setClass("text");
				$form->addPassword('password', 'Password:')
					->addRule(Form::FILLED, 'Please fill password.')
					->getControlPrototype()
					->setClass("text");
				$form->addSubmit('submitted', 'Login');
				$form->onSubmit[] = array($this, 'loginFormSubmitted');
				return $form;
				break;
			case 'addForm':
				$form = $this->prepareForm();

				$form->onSubmit[] = array($this, 'addFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'editForm':
				$form = $this->prepareForm('edit');

				$id = $this->getParameter('id');
				if ($id == 1) {
					throw new BadRequestException();
				}
				$model = $this->users;
				$values = $model->find($id);
				$form->setDefaults($values);

				$form->onSubmit[] = array($this, 'editFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'removeForm':
				$id = $this->getParameter('id');
				if ($id == 1) {
					throw new BadRequestException();
				}
				$form = new BaseForm;
				$form->getRenderer()->wrappers['controls']['container'] = NULL;
				$form->confirmAndProcess($this, 'deleteFormSubmitted');
				return $form;
				break;
			case 'changeForm':
				$form = new  BaseForm;
				$password = $form->addPassword("password", "Password:");
				$password->getControlPrototype()
					->class('text');
				$password->addRule(Form::FILLED, "Fill the password.");
				$again = $form->addPassword("again", "Password again:");
				$again->addRule(Form::FILLED, "Fill the second password for check.");
				$again->addCondition(Form::FILLED, $form["again"])
					->addRule(Form::EQUAL, "Passwords has to match.", $form["password"]);

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

	protected function prepareForm($owner = "add")
	{
		$form = new BaseForm;
		$form->addText("username", "Username:")
			->addRule(Form::FILLED, "Fill the username.")
			->getControlPrototype()
			->class('text');
		$password = $form->addPassword("password", "Password:");
		$password->getControlPrototype()
			->class('text');
		if ($owner == "add") {
			$password->addRule(Form::FILLED, "Fill the password.");
		}

		$again = $form->addPassword("again", "Password again:");
		if ($owner == "add") {
			$again->addRule(Form::FILLED, "Fill the second password for check.");
			$again->addCondition(Form::FILLED, $form["again"])
				->addRule(Form::EQUAL, "Passwords has to match.", $form["password"]);
		}
		$again->getControlPrototype()
			->class('text');

		if ($owner == "edit") {
			$password->addConditionOn($again, Form::FILLED)
				->addRule(Form::FILLED, "Fill the password");
			$again->addConditionOn($password, Form::FILLED)
				->addRule(Form::FILLED, "Fill the second password for check.");
			$again->addCondition(Form::FILLED, $form["again"])
				->addRule(Form::EQUAL, "Passwords has to match.", $form["password"]);
		}

		$form->addSelect('role', 'Role', array("normal" => "Normal", "master" => "Master"))
			->addRule(Form::FILLED, "Choose role.");
		if ($owner == "edit") {
			$form->addSubmit("submitted", "Edit");
		} else {
			$form->addSubmit("submitted", "Add");
		}
		return $form;
	}
}
