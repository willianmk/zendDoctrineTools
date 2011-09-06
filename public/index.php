<?php

error_reporting(E_ALL);
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath('../application'));

define("PUBLIC_PATH", ".");
define("LIB_JS", PUBLIC_PATH . "/js");
define("LIB_CSS", PUBLIC_PATH . "/css");

define("ADMIN_MODULE", "admin");
define("DEFAULT_MODULE", "default");

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath('../library'),
            realpath(APPLICATION_PATH . "/models"),
            realpath(APPLICATION_PATH . "/models/generated"),
            get_include_path(),
        )));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();