<?php

$sort_type = $_GET['sort_type'] ?? null;

$title = $_GET['title'] ?? null;

$star_name = $_GET['star_name'] ?? null;

switch ($sort_type) {
    case 'alphabetical':
        $movies_list = Movie::getByAlphabeticalTitle($title, $star_name);
        break;

    default:
        $movies_list = Movie::getWithoutSorting($title, $star_name);
        break;
}

echo json_encode($movies_list);
