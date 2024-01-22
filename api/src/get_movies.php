<?php

// Отримуємо необхідні параметри
$sort_type = $_GET['sort_type'] ?? null;
$title = $_GET['title'] ?? null;
$star_name = $_GET['star_name'] ?? null;

// Отримуємо із БД фільм відповідно до типу сортування та додаткових параметрів
$movies_list = Movie::getAll($sort_type, $title, $star_name);

// Видаємо JSON з фільмами
echo json_encode($movies_list);
