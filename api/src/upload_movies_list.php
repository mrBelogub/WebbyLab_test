<?php

// Перевіряємо що з завантаженням файлу все ОК
if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
    throw new Exception("Помилка під час завантаження файлу.");
}

// Перевіряємо, чи формат файлу txt
$file_name = $_FILES["file"]["name"];
$allowed_extensions = ["txt"];
Validator::checkFileExtension($file_name, $allowed_extensions);

// Перевіряємо, чи не порожній файл
$file_size = $_FILES["file"]["size"];
Validator::checkFileIsntEmpty($file_size);

// "Відкриваємо" файл
$uploadedFile = $_FILES["file"];
$fileTmpName = $uploadedFile["tmp_name"];

// Отрмуємо дані з файлу
$movies_list = file_get_contents($fileTmpName);

// Парсимо та додаємо фільми та зірок в БД
Movie::uploadFromList($movies_list);
