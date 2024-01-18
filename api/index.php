<?php

session_start();

$action = $_GET['action'] ?? null;

if(empty($action)) {
    echo 'Unknown action';
    http_response_code(400);
    exit;
}

if ($action != "login") {
    if (!isset($_SESSION['user'])) {
        echo "401 Unauthorized";
        http_response_code(401);
        exit;
    }
}

$file_path = "src/" . $action . ".php";

if (!file_exists($file_path)) {
    echo 'Action not found';
    http_response_code(404);
    exit;
}

require_once "src/Core/requires.php";

try {
    require_once $file_path;
} catch (Exception $e) {
    // Отримання значення message
    $errorMessage = $e->getMessage();

    //TODO: Отримання коду помилки
    $errorCode = $e->getCode();

    echo "Error message: $errorMessage";
    http_response_code(400);
    exit;
}
