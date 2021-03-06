<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}
defined('BASE_PATH') or define('BASE_PATH', realpath(dirname(__DIR__)));
defined('PUBLIC_PATH') or define('PUBLIC_PATH', BASE_PATH . '/public');
defined('DATA_PATH') or define('DATA_PATH', BASE_PATH . '/data');
// Setup autoloading
require 'init_autoloader.php';
// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
