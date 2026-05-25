<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Normalize URI
$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = strtok($requestUri, '?');

if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}

// Routing
switch ($requestUri) {

    case '/api/login':
        if (file_exists('api/login/index.php')) {
            require 'api/login/index.php';
        } else {
            http_response_code(500);
            echo json_encode(["message" => "File api/login/index.php tidak ditemukan!"]);
        }
        break;

    case '/api/register':
        if (file_exists('api/register/index.php')) {
            require 'api/register/index.php';
        } else {
            http_response_code(500);
            echo json_encode(["message" => "File api/register/index.php tidak ditemukan!"]);
        }
        break;

    case '/api/users':
        if (file_exists('api/users/index.php')) {
            require 'api/users/index.php';
        } else {
            http_response_code(500);
            echo json_encode(["message" => "File api/users/index.php tidak ditemukan!"]);
        }
        break;

    case '/':

    case '/api/cicd':
        http_response_code(200);
        echo json_encode(["message" => "CI/CD is working"]);
        break;

    case '/docker':
        http_response_code(200);
        echo json_encode(["message" => "Docker is working"]);
        break;

    case '/index.php':
        http_response_code(200);
        echo json_encode(["message" => "Welcome to the API service"]);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "message" => "Endpoint not found.",
            "debug_uri" => $requestUri
        ]);
        break;
}
?>