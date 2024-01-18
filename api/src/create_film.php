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

$movie_id = Films::create($title, $release_year, $format);

$stars_array = explode(",", $stars);

foreach ($stars_array as $current_star_name) { 
    $trimmed_current_star_name = trim($current_star_name); 
    $actor_id = Stars::getIdByName($trimmed_current_star_name);
    if(!$actor_id) {
        $actor_id = Stars::create($current_star_name);
    }

    Stars::addToFilm($actor_id, $movie_id);
}