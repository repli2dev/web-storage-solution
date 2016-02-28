<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('download/<hash>', [
			'presenter' => 'Files',
			'action' => 'download',
			'hash' => NULL
		]);
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Files:default');
		return $router;
	}

}
