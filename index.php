<?php

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$url = array_map('strtolower', $url);

// Check the first part of the url for the controller
$controller_name = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';

// Check the second part the url for the action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Check if the controller and the action exists in the system
if (!file_exists('app/controllers/' . $controller_name . '.php')) {
    die('Controller not found');
}

require_once 'app/controllers/' . $controller_name . '.php';

$controller = new  $controller_name();

if (!method_exists($controller, $action)) {
    die('Action not found: ' . $action);
}

// Call action with remaining parameters (if exists)
call_user_func_array([$controller, $action], array_slice($url, 2));