<?php

$sort_type = $_GET['sort_type'] ?? null;

$title = $_GET['title'] ?? null;

$star_name = $_GET['star_name'] ?? null;

switch ($sort_type) {
    case SORT_TYPE_APLHABETICAL:
        $films_list = Films::getByAlphabeticalTitle($title, $star_name);
        break;
    
    default:
        $films_list = Films::getWithoutSorting($title, $star_name);
        break;
}

echo json_encode($films_list);

