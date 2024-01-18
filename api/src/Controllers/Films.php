<?php

class Films{

    public static function getWithoutSorting($title = null){
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            WHERE `films`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `films`.`title`) 
                            GROUP BY `films`.`id`", ["title" => $title]);

        return $films_list;   
    }

    public static function getByAlphabeticalTitle($title = null){
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            WHERE `films`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `films`.`title`) 
                            GROUP BY `films`.`id`
                            ORDER BY `films`.`title` ASC", ["title" => $title]);

        return $films_list;   
    }
}