<?php

/**
 * Own storage web
 *
 * @copyright  Copyright (c) 2010 Jan Drabek
 * @package    Own storage web
 */

// Load Nette
require LIBS_DIR . '/Nette/loader.php';


// Load config
Environment::loadConfig();


// Debugging?
$debug = Environment::getConfig("debug");

if($debug->enable){
	Debug::enable();
}

$application = Environment::getApplication();
$application->errorPresenter = 'Error';
$application->catchExceptions = Environment::isProduction() ? TRUE : FALSE;

// Set encoding (server is probably cp1250)
$httpResponse = Environment::getHttpResponse();
$httpResponse ->setContentType( 'text/html' , 'UTF-8' ); 

// Settup basic routes
$router = $application->getRouter();

$router[] = new Route('index.php', array(
	'presenter' => 'Files',
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('download/<hash>', array(
	'presenter' => 'Files',
	'action' => 'download',
	'hash'	=> NULL
));

$router[] = new Route('<presenter>/<action>/<id>', array(
	'presenter' => 'Files',
	'action' => 'default',
	'id' => NULL,
));

// Connect to DB
dibi::connect(Environment::getConfig('database'));

// Run the application
$application->run();
