<?php
namespace App\Presenters;

use App\Components\BaseForm;
use App\Model\Files;
use DateTime;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;

class FilesPresenter extends BasePresenter
{
	/** @var Files */
	private $files;

	public function injectFiles(Files $files)
	{
		$this->files = $files;
	}

	public function actionDefault($hash = NULL)
	{
		if (!$this->canSee("files")) {
			$this->redirect("Users:login");
		} else {
			$id = $this->getUser()->getId();

			$data = $this->files->findByUser($id);

			$this->getTemplate()->data = $data;
		}
	}

	public function actionDownload($hash = NULL)
	{
		//if(!$this->canSee("stored-files")){
		//	$this->redirect("Users:login");
		//} else {
		if ($hash != NULL) {
			$model = $this->files;
			$data = $model->findByHash($hash)->fetchAll();
			if (file_exists('./stored-files/' . $data[0]->hash)) {
				$response = $this->getHttpResponse();
				$response->setHeader('Content-type', 'application/octet-stream');
				$response->setHeader('Content-Disposition', 'attachment; filename="' . $data[0]->real_name . '"');
				$response->setHeader('Pragma', 'no-cache');
				$response->setHeader('Expires', '0');
				$file = fopen('./stored-files/' . $data[0]->hash, "rb");
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

	public function actionAdd()
	{
		if (!$this->canSee("files")) {
			$this->redirect("Users:login");
		}
	}

	public function actionEdit($id)
	{
		if (!$this->canSee("files")) {
			$this->redirect("Users:login");
		}
	}

	public function actionRemove($id)
	{
		if (!$this->canSee("files")) {
			$this->redirect("Users:login");
		} else {
			$model = $this->files;
			$data = $model->find($id);
			$user = $this->getUser()->getId();
			if ($data->user != $user) throw new BadRequestException();
			$this->getTemplate()->data = $data;
		}
	}

	public function actionProgress($progress_key)
	{
		if (isset($progress_key)) {
			$status = apc_fetch('upload_' . $progress_key);
			echo $status['current'] / $status['total'] * 100;
			die;
		}
	}

	public function actionMaintenance($key)
	{
		$variable = $this->context->parameters['variable'];
		if ($key != $variable['key']) {
			throw new BadRequestException();
		} else {
			$model = $this->files;
			$allFiles = $model->findExpired();
			if (count($allFiles) > 0) {
				foreach ($allFiles as $file) {
					if (file_exists('./stored-files/' . $file->hash)) {
						unlink('./stored-files/' . $file->hash);
						$model->delete($file->id);
					}
				}
			}
			$this->terminate();
		}
	}

	// SIGNALS and HANDLERS

	public function addFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		// Detach file
		$file = $values["file"];
		unset($values["file"]);
		$incoming = $this->getIncomingFiles();
		$fileIncoming = null;
		if (count($incoming) != 0) {
			$fileIncoming = $incoming[$values["fileIncoming"]];
		}
		unset($values["fileIncoming"]);

		$name = Strings::webalize($file->getName(), ".");
		$name = str_replace("..", "", $name);

		// Check if one file was provided
		if (empty($fileIncoming) && empty($name)) {
			$form->addError("Provide files one file to store (by uploading or selection).");
		} else {
			if (!empty($fileIncoming) && !empty($name)) {
				$form->addError("You uploaded file as well you have selected one from incoming. Please select only one way of uploading!");
				$form["fileIncoming"]->setValue();
			} else {
				$user = $this->getUser()->getId();
				$values["user"] = $user;
				$values["uploaded"] = new DateTime();
				$model = $this->files;
				$id = $model->add($values);
				if (empty($fileIncoming)) {
					$values["real_name"] = $name;
					$values["hash"] = md5($user . '_' . $id . '_' . $values['real_name']);
					$file->move('./stored-files/' . $values["hash"]);
				} else {
					$values["real_name"] = $fileIncoming;
					$values["hash"] = md5($user . '_' . $id . '_' . $values['real_name']);
					if (file_exists("./incoming/" . $fileIncoming) && !is_dir("./incoming/" . $fileIncoming)) {
						rename("./incoming/" . $fileIncoming, './stored-files/' . $values["hash"]);
					} else {
						$form->addError("Error when moving file from incoming, please check permissions.");
						$model->delete($id);
						return;
					}
				}
				$model->edit($id, $values);

				// Create message and redirect back
				$this->flashMessage('File was successfully added.', 'success');
				$this->redirect("default");
			}
		}
	}

	public function editFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		$id = $this->getParameter('id');

		$model = $this->files;
		$model->edit($id, $values);
		// Create message and redirect back
		$this->flashMessage('File was successfully edited.', 'success');
		$this->redirect("default");
	}

	public function deleteFormSubmitted(Form $form)
	{
		// Check if form was submitted by yes button
		if ($form["yes"]->isSubmittedBy()) {
			// Get id of item to delete
			$id = $this->getParameter('id');
			// Delete item
			$model = $this->files;
			$data = $model->find($id);
			$hash = $data->hash;
			if (file_exists('./stored-files/' . $hash)) {
				unlink('./stored-files/' . $hash);
			}
			$model->delete($id);
			// Create message and redirect back
			$this->flashMessage('File was successfully deleted.', 'success');
			$this->redirect('default');
		} else {
			$this->redirect('default');
		}
	}

	// PROTECTED

	protected function createComponent($name)
	{
		switch ($name) {
			case 'addForm':
				$form = $this->prepareForm();

				$form->onSubmit[] = array($this, 'addFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'editForm':
				$form = $this->prepareForm('edit');

				$id = $this->getParameter('id');
				$model = $this->files;
				$values = $model->find($id);
				$user = $this->getUser()->getId();
				if ($values->user != $user) throw new BadRequestException ();
				$form->setDefaults($values);

				$form->onSubmit[] = array($this, 'editFormSubmitted');

				$this->addComponent($form, $name);
				break;
			case 'removeForm':
				$id = $this->getParameter('id');
				$form = new BaseForm;
				$form->getRenderer()->wrappers['controls']['container'] = NULL;
				$form->confirmAndProcess($this, 'deleteFormSubmitted');
				return $form;
				break;
			default:
				break;
		}
	}

	protected function prepareForm($owner = "add")
	{
		$form = new BaseForm;
		if ($owner == "add") {
			$uploadLimit = (int)(ini_get('upload_max_filesize'));
			$postLimit = (int)(ini_get('post_max_size'));
			$limit = min($postLimit, $uploadLimit);
			$form->addUpload("file", "File to store:");
			if ($uploadLimit !== false && $postLimit !== false && $limit > 0) {
				$form['file']->setOption('description', "The file has to be smaller than $limit MB.");
				$form['file']->addRule(Form::MAX_FILE_SIZE, "The file has to be smaller than $limit MB.", $limit * 1024 * 1024);
			}
			$incoming = array(0 => "Choose file") + $this->getIncomingFiles();
			$form->addSelect("fileIncoming", "File from incoming: ", $incoming);
		}

		$form->addDateTimePicker('expire', 'Date and time of expiring:', 16)
			->addRule(Form::FILLED, 'Enter date and time of expiring.');

		if ($owner == "edit") {
			$form->addSubmit("submitted", "Edit");
		} else {
			$form->addSubmit("submitted", "Add")
				->getControlPrototype()->setId("uploadButton");

		}
		return $form;
	}

	private function getIncomingFiles()
	{
		$files = array();
		$i = 1;
		if ($handle = opendir('./incoming/')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file != ".placeholder" && $file != ".htaccess" && $file != "web.config") {
					$files[$i++] = $file;
				}
			}
			closedir($handle);
		}
		uasort($files, "strcasecmp");
		return $files;
	}

}
