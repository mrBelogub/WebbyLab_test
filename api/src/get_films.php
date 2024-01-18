<?php

$sort_type = $_GET['sort_type'] ?? null;

$title = $_GET['title'] ?? null;

$actor_name = $_GET['actor_name'] ?? null;

switch ($sort_type) {
    case SORT_TYPE_APLHABETICAL:
        $films_list = Films::getByAlphabeticalTitle($title, $actor_name);
        break;
    
    default:
        $films_list = Films::getWithoutSorting($title, $actor_name);
        break;
}

echo json_encode($films_list);

