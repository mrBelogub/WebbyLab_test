<?php

class Films{

    public static function getWithoutSorting($title = null, $actor_name = null){
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            WHERE `films`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `films`.`title`)
                            AND `stars`.`name` LIKE COALESCE(CONCAT('%', :actor_name, '%'), `stars`.`name`)
                            GROUP BY `films`.`id`", ["title" => $title, "actor_name" => $actor_name]);
        return $films_list;   
    }

    public static function getByAlphabeticalTitle($title = null, $actor_name = null){
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            WHERE `films`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `films`.`title`) 
                            AND `stars`.`name` LIKE COALESCE(CONCAT('%', :actor_name, '%'), `stars`.`name`)
                            GROUP BY `films`.`id`
                            ORDER BY `films`.`title` ASC", ["title" => $title, "actor_name" => $actor_name]);

        return $films_list;   
    }
}