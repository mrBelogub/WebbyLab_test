<?php

if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
    throw new Exception("Помилка під час завантаження файлу.");
}

$uploadedFile = $_FILES["file"];

$fileTmpName = $uploadedFile["tmp_name"];

$films_list = file_get_contents($fileTmpName);

$movies = explode("\n\n", $films_list);

Films::uploadFromList($movies);