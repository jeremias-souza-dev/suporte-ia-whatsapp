<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());


// Save any POST or GET request to a file in JSON format
$data = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'timestamp' => date('Y-m-d H:i:s'),
    'get' => $_GET,
    'post' => $_POST,
];

$file = __DIR__ . '/requests.json';
$existingData = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$existingData[] = $data;

file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT));