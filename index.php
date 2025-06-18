<?php

require_once 'app/helpers/SessionHelper.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/AccountModel.php';
require_once 'app/models/OAuthAccountModel.php';
require_once 'app/controllers/OAuthController.php';
require_once 'vendor/autoload.php';
require_once 'app/controllers/ProductApiController.php';
require_once 'app/controllers/CategoryApiController.php';


// Sanitize URL
// First urldecode the URL
$url = $_GET['url'] ?? '';
$url = urldecode($url);

// Parse the URL
$parsed = parse_url($url);

// Handle path components more carefully
$pathComponents = [];
if (isset($parsed['path'])) {
    $pathComponents = explode('/', $parsed['path']);
    $pathComponents = array_filter($pathComponents); // Remove empty segments
}

// Only sanitize for directory traversal, not special characters
$pathComponents = array_map(function ($segment) {
    // Remove directory traversal attempts but preserve most characters
    $segment = str_replace(['../', '..\\', '%00'], '', $segment);
    return $segment;
}, $pathComponents);

// Check the first part of the url for the controller
$controller_name = isset($pathComponents[0]) && $pathComponents[0] != '' ? ucfirst($pathComponents[0]) . 'Controller' : 'DefaultController';

// Check the second part the url for the action
$action = isset($pathComponents[1]) && $pathComponents[1] != '' ? $pathComponents[1] : 'index';


// Check if the controller is an API controller and if the action exists
if ($controller_name === 'ApiController' && isset($pathComponents[1])) {
    $apiController = ucfirst($pathComponents[1]) . 'ApiController';

    if (file_exists('app/controllers/' . $apiController . '.php')) {
        require_once 'app/controllers/' . $apiController . '.php';
        $controller = new $apiController();

        $method = $_SERVER['REQUEST_METHOD'];

        // // Get parameters from sanitized query string
        // $params = $getParams;

        // Remove 'url' from params as it's used for routing
        // unset($params['url']);

        // Get ID from query parameter if it exists
        // $id = $params['id'] ?? $url[3] ?? null;
        $id = $pathComponents[2] ?? null;

        if (count($pathComponents) > 3) {
            if ($pathComponents[3] != null || $pathComponents[2] == 'search') {
                $id = $pathComponents[3];
            }
        }

        switch ($method) {
            case 'GET':
                if (!isset($pathComponents[2])) {
                    $action = 'list';
                } else {
                    $action = ($pathComponents[2] == 'test_connection') ? 'test_connection' : ($id ? 'show' : 'list');
                }
                break;
            case 'POST':
                $action = 'store';
                // Use sanitized POST data
                // $params = $postParams;
                break;
            case 'PUT':
                if ($id)
                    $action = 'update';

                break;
            case "DELETE":
                if ($id)
                    $action = 'delete';

                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method not allowed']);
                exit;
        }

        if (method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                // Pass all sanitized parameters to the method
                // call_user_func_array([$controller, $action], [$params]);
                call_user_func_array([$controller, $action], []);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'API controller not found']);
        exit;
    }
}

// Check if the controller and the action exists in the system
if (!file_exists('app/controllers/' . $controller_name . '.php')) {
    die('Controller not found');
} else {
    require_once 'app/controllers/' . $controller_name . '.php';
    $controller = new $controller_name();
}

if (!method_exists($controller, $action)) {
    die('Action not found: ' . $action);
} else {
    // Call action with remaining parameters (if exists)
    $params = array_slice($pathComponents, 2);
    if (count($params) > 0) {
        call_user_func_array([$controller, $action], $params);
    } else {
        call_user_func([$controller, $action]);
    }
}
