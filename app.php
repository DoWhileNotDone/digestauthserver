<?php declare(strict_types=1);

session_start();
date_default_timezone_set("UTC");

require __DIR__ . "/vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Instantiate the app
$applicationsettings = require __DIR__ . '/config/settings.php';

// To be correctly placed in the app settings, the sub array is required
if ($applicationsettings['settings']['displayErrorDetails']) {
    error_reporting(E_ALL);
    ini_set("display_errors", "true");
}

$app = new \Slim\App($applicationsettings);

require __DIR__ . "/config/dependencies.php";
require __DIR__ . "/config/middleware.php";

# Define Routes
require __DIR__ . "/routes/index.php";

$app->run();
