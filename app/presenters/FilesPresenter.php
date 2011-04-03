<?php

/**
 * Files presenter
 *
 * @author     Jan Drabek
 * @package    Own storage web
 */
class FilesPresenter extends BasePresenter {

	public function actionDefault($hash = NULL) {
		if(!$this->canSee("files")){
			$this->redirect("Users:login");
		} else {
			$id = Environment::getUser()->getIdentity()->id;
			
			$model = new FilesModel();
			$data = $model->findByUser($id);

			$this->getTemplate()->data = $data;
		}
	}

	public function actionDownload($hash = NULL) {
		//if(!$this->canSee("files")){
		//	$this->redirect("Users:login");
		//} else {
			if($hash != NULL){
				$model = new FilesModel();
				$data = $model->findByHash($hash)->fetchAll();
				if(file_exists('./files/'.$data[0]->hash)){
					$response = Environment::getHttpResponse();
					$response->setHeader('Content-type', 'application/octet-stream');
					$response->setHeader('Content-Disposition', 'attachment; filename="'.$data[0]->real_name.'"');
					$response->setHeader('Pragma', 'no-cache');
					$response->setHeader('Expires', '0');
					$file = fopen('./files/'.$data[0]->hash,"rb");
					while (!feof($file)) {
						$line = fgets($file);
						echo $line;
						flush();
					}
					$this->terminate();
				} else {
					throw new BadRequestException();
				}
			} else {
				throw new BadRequestException();
			}
		//}
	}

	public function actionAdd() {
		if(!$this->canSee("files")){
			$this->redirect("Users:login");
		}
	}

	public function actionEdit($id) {
		if(!$this->canSee("files")){
			$this->redirect("Users:login");
		}
	}

	public function actionRemove($id) {
		if(!$this->canSee("files")){
			$this->redirect("Users:login");
		} else {
			$model = new FilesModel();
			$data = $model->find($id);
			$user = Environment::getUser()->getIdentity()->id;
			if($data->user != $user) throw new BadRequestException ();
			$this->getTemplate()->data = $data;
		}
	}

	public function actionProgress($progress_key) {
		if(isset($progress_key)) {
			$status = apc_fetch('upload_'.$progress_key);
			echo $status['current']/$status['total']*100;
			die;
		}
	}

	public function actionMaintainance($key) {
		$variable = Environment::getConfig('variable');
		if($key != $variable->key){
			throw new BadRequestException();
		} else {
			$model = new FilesModel();
			$allFiles = $model->findExpired();
			if(count($allFiles) > 0){
				foreach($allFiles as $file){
					if(file_exists('./files/'.$file->hash)){
						unlink('./files/'.$file->hash);
						$model->delete($file->id);
					}
				}
			}
			$this->terminate();
		}
	}

	// SIGNALS and HANDLERS

	public function addFormSubmitted(Form $form) {
		// Get values
		$values = $form->getValues();
		// Detach file
		$file = $values["file"];
		unset($values["file"]);
		$incoming = $this->getIncomingFiles();
		$fileIncoming = $incoming[$values["fileIncoming"]];
		unset($values["fileIncoming"]);

		$name = String::webalize($file->getName(),".");
		$name = str_replace("..","",$name);

		// Check if one file was provided
		if(empty($fileIncoming) && empty($name)) {
			$form->addError("Provide files one file to store (by uploading or selection).");
		} else {
			if(!empty($fileIncoming) && !empty($name)) {
				$form->addError("You uploaded file as well you have selected one from incoming. Please select only one way of uploading!");
				$form["fileIncoming"]->setValue();
			} else {
				$user = Environment::getUser()->getIdentity()->id;
				$values["user"] = $user;
				$values["uploaded"] = new DateTime();
				$model = new FilesModel();
				$id = $model->add($values);
				if(empty($fileIncoming)) {
					$values["real_name"] = $name;
					$values["hash"] = md5($user.'_'.$id.'_'.$values['real_name']);
					$file->move('./files/'.$values["hash"]);
				} else {
					$values["real_name"] = $fileIncoming;
					$values["hash"] = md5($user.'_'.$id.'_'.$values['real_name']);
					if(file_exists("./incoming/".$fileIncoming) && !is_dir("./incoming/".$fileIncoming)) {
						rename("./incoming/".$fileIncoming,'./files/'.$values["hash"]);
					} else {
						$form->addError("Error when moving file from incoming, please check permissions.");
						$model->delete($id);
						return;
					}
				}
				$model->edit($id,$values);
				
				// Create message and redirect back
				$this->flashMessage('File was successfully added.','success');
				$this->redirect("default");
			}
		}
	}

	public function editFormSubmitted(Form $form) {
		$id = $this->getParam('id');
		$values = $form->getValues();
		
		$model = new FilesModel();
		$model->edit($id,$values);
		// Create message and redirect back
		$this->flashMessage('File was successfully edited.','success');
		$this->redirect("default");
	}

	public function deleteFormSubmitted(Form $form){
		// Check if form was submitted by yes button
		if($form["yes"]->isSubmittedBy()){
			// Get id of item to delete
			$id = $this->getParam('id');
			// Delete item
			$model = new FilesModel();
			$data = $model->find($id);
			$hash = $data->hash;
			if(file_exists('./files/'.$hash)) {
				unlink('./files/'.$hash);
			}
			$model->delete($id);
			// Create message and redirect back
			$this->flashMessage('File was successfully deleted.','success');
			$this->redirect('default');
		} else {
			$this->redirect('default');
		}
	}

	// PROTECTED

	protected function createComponent($name) {
		switch($name) {
			case 'addForm':
				$form = $this->prepareForm();

				$form->onSubmit[] = array($this,'addFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'editForm':
				$form = $this->prepareForm('edit');

				$id = $this->getParam('id');
				$model = new FilesModel();
				$values = $model->find($id);
				$user = Environment::getUser()->getIdentity()->id;
				if($values->user != $user) throw new BadRequestException ();
				$form->setDefaults($values);

				$form->onSubmit[] = array($this,'editFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'removeForm':
				$id = $this->getParam('id');
				$form = new BaseForm;
				$form->getRenderer()->wrappers['controls']['container'] = NULL;
				$form->confirmAndProcess($this, 'deleteFormSubmitted');
				return $form;
				break;
			default:
				break;
		}
	}

	protected function prepareForm($owner = "add"){
		$form = new BaseForm;
		if($owner == "add") {
			$uploadLimit = (int)(ini_get('upload_max_filesize'));
			$postLimit = (int)(ini_get('post_max_size'));
			$limit = min($postLimit, $uploadLimit);
			$form->addFile("file","File to store:");
			if ($uploadLimit !== false && $postLimit !== false && $limit > 0) {
				$form['file']->setOption('description', "The file has to be smaller than $limit MB.");
				$form['file']->addRule(Form::MAX_FILE_SIZE, "The file has to be smaller than $limit MB.", $limit * 1024 * 1024);
			}
			$incoming = array(0 => "Choose file") + $this->getIncomingFiles();
			$form->addSelect("fileIncoming","File from incoming: ",$incoming);
		}

		$form->addDateTimePicker('expire', 'Date and time of expiring:', 16, 16)
			->addRule(Form::FILLED, 'Enter date and time of expiring.');

		if($owner == "edit"){
			$form->addSubmit("submitted", "Edit");
		} else {
			$form->addSubmit("submitted", "Add")
				->getControlPrototype()->setId("uploadButton");

		}
		return $form;
	}

	private function getIncomingFiles() {
		$files = array();
		$i = 1;
		if ($handle = opendir('./incoming/')) {
			while (false !== ($file = readdir($handle))) {
				if($file != "." && $file != ".." && $file != ".placeholder") {
					$files[$i++] = $file;
				}
			}
			closedir($handle);
		}
		uasort($files,"strcasecmp");
		return $files;
	}

}
