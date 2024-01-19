<?php

// Розпочинаємо сессію
session_start();

// Отримуємо що там хотів від нас користувач
$action = $_GET['action'] ?? null;

// Якщо action прийшов пустим - видаємо помилку
if(empty($action)) {
    echo 'Unknown action';
    http_response_code(400);
    exit;
}

// Якщо користувач хотів не авторизуватись - перевіряємо, чи авторизованний він
if ($action != "login") {
    if (!isset($_SESSION['user'])) {
        echo "401 Unauthorized";
        http_response_code(401);
        exit;
    }
}

// Формуємо шлях до файлу обробника
$file_path = "src/" . $action . ".php";

// Якщо в нас нема такого обробника - видаємо помилку
if (!file_exists($file_path)) {
    echo 'Action not found';
    http_response_code(404);
    exit;
}

// Підключаємося до файлу залежностей
require_once "src/Core/requires.php";

try {
    // Намагаємося виконати дію
    require_once $file_path;
} catch (Exception $e) {
    // Якщо чомусь не виходить - видаємо помилку
    $errorMessage = $e->getMessage();

    echo "Error message: $errorMessage";
    http_response_code(400);
    exit;
}
