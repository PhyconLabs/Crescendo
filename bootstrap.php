<?php
namespace Crescendo;

/**
 * Define all required constants under Crescendo namespace.
 */

const VERSION = "1.0-alpha"; // Crescendo version
const ROOT_PATH = __DIR__; // Crescendo root path

// Only define APPLICATION_ROOT_PATH if it's not already defined ( to support custom composer structures ).
if (!defined("\\Crescendo\\APPLICATION_ROOT_PATH")) {
    define("Crescendo\\APPLICATION_ROOT_PATH", realpath(ROOT_PATH . "/../../.."));
}

// Only define COMPOSER_ROOT_PATH if it's not already defined ( to support custom composer structures ).
if (!defined("\\Crescendo\\COMPOSER_ROOT_PATH")) {
    define("Crescendo\\COMPOSER_ROOT_PATH", APPLICATION_ROOT_PATH . "/vendor");
}

require COMPOSER_ROOT_PATH . "/autoload.php"; // Rely on Composer for all autoloading goodness.

// Load application bootstrap file if it exists to allow tapping into booting process very early on.
$applicationBootstrapPath = APPLICATION_ROOT_PATH . "/bootstrap.php";
if (file_exists($applicationBootstrapPath)) {
    require $applicationBootstrapPath;
}

if (!isset($application)) {
    $application = Application::init();
}