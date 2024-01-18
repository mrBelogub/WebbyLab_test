<?php

$action = $_GET['action'] ?? null;

if(empty($action)){
    echo 'unknown action';
    http_response_code(400);
    exit;
}

$file_path = "src/" . $action . ".php";

if (!file_exists($file_path)) {
    echo 'Action not found';
    http_response_code(404);
    exit;
}

require_once "src/Core/requires.php";

try{
    require_once $file_path;
}
catch (Exception $e){
    // Отримання значення message
    $errorMessage = $e->getMessage();

    //TODO: Отримання коду помилки
    $errorCode = $e->getCode();

    echo "Error message: $errorMessage";
    http_response_code(400);
    exit;
}


