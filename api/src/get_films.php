<?php

$sort_type = $_GET['sort_type'] ?? null;

switch ($sort_type) {
    case SORT_TYPE_APLHABETICAL:
        $films_list = Films::getByAlphabeticalTitle();
        break;
    
    default:
        $films_list = Films::getWithoutSorting();
        break;
}

echo json_encode($films_list);

