<?php

$title = $_POST["title"] ?? null;
$release_year = $_POST["release_year"] ?? null;
$format = $_POST["format"] ?? null;
$stars = $_POST["stars"] ?? null;

Validator::isEmpty("Назва", $title);
Validator::isEmpty("Рік випуску", $release_year);
Validator::isEmpty("Формат", $title);
Validator::isEmpty("Зірки", $stars);

Validator::isMovieFormatAcceptable($format);

$movie_id = Movie::create($title, $release_year, $format);

$stars_array = explode(",", $stars);

foreach ($stars_array as $current_star_name) { 
    $trimmed_current_star_name = trim($current_star_name); 
    $star_id = Star::getIdByName($trimmed_current_star_name);
    if(!$star_id) {
        $star_id = Star::create($current_star_name);
    }

    Star::addToMovie($star_id, $movie_id);
}