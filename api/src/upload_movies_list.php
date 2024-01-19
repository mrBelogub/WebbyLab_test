<?php

// Перевіряємо що з завантаженням файлу все ОК
if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
    throw new Exception("Помилка під час завантаження файлу.");
}

// "Відкриваємо" файл
$uploadedFile = $_FILES["file"];
$fileTmpName = $uploadedFile["tmp_name"];

// Отрмуємо дані з файлу
$movies_list = file_get_contents($fileTmpName);

// Парсимо та додаємо фільми та зірок в БД
Movie::uploadFromList($movies);
