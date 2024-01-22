<?php

// Отримуємо необхідні параметри
$title = $_POST["title"] ?? null;
$release_year = $_POST["release_year"] ?? null;
$format = $_POST["format"] ?? null;
$stars = $_POST["stars"] ?? null;

// Перевіяємо чи нема випадково пустого значення
Validator::isEmpty("Назва", $title);
Validator::isEmpty("Рік випуску", $release_year);
Validator::isEmpty("Формат", $title);
Validator::isEmpty("Зірки", $stars);

// Перевіємо чи підходить формат фільму
Validator::isMovieFormatAcceptable($format, $title);

// Перевіряємо, чи входить рік виходу в допустимий діапазон
Validator::isMovieReleaseYearInAcceptableRange($release_year, $title);

// Створюємо фільм та отримуємо його ID
$movie_id = Movie::create($title, $release_year, $format);

// Розбиваємо зірок на кожну окрему
$stars_array = explode(",", $stars);

// Створюємо кожну зірку та прив'язємо до фільму
foreach ($stars_array as $current_star_name) {
    // Видаляємо зайві пробіли в імені
    $trimmed_current_star_name = trim($current_star_name);

    // Перевіяємо чи нема зірки з цим іменем в бд
    $star_id = Star::getIdByName($trimmed_current_star_name);
    if(!$star_id) {
        // Якщо нема - створюємо зірку та отримуємо ID
        $star_id = Star::create($trimmed_current_star_name);
    }

    // Прив'язуємо зірку до фільму
    Star::addToMovie($star_id, $movie_id);
}
