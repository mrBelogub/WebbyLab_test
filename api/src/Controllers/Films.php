<?php

class Films{

    public static function getWithoutSorting(){
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            GROUP BY `films`.`id`", []);

        return $films_list;   
    }

    public static function getByAlphabeticalTitle(){
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            GROUP BY `films`.`id`
                            ORDER BY `films`.`title` ASC", []);

        return $films_list;   
    }
}