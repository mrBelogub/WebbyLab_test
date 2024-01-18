<?php

$sort_type = $_GET['sort_type'] ?? null;

$title = $_GET['title'] ?? null;

switch ($sort_type) {
    case SORT_TYPE_APLHABETICAL:
        $films_list = Films::getByAlphabeticalTitle($title);
        break;
    
    default:
        $films_list = Films::getWithoutSorting($title);
        break;
}

echo json_encode($films_list);

