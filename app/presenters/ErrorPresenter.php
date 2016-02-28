<?php
namespace App\Presenters;

use Nette\Application\BadRequestException;
use Tracy\Debugger;

class ErrorPresenter extends BasePresenter
{

	/**
	 * @param  Exception
	 * @return void
	 */
	public function renderDefault($exception)
	{
		if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = TRUE;
			$this->terminate();

		} elseif ($exception instanceof BadRequestException) {
			$this->setView('404'); // load template 404.phtml

		} else {
			$this->setView('500'); // load template 500.phtml
			Debugger::log($exception); // and handle error by Debug
		}
	}

}
