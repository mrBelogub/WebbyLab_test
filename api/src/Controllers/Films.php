<?php

class Films
{
    public static function getWithoutSorting($title = null, $actor_name = null)
    {
        $films_list = DB::q("SELECT `films`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `actors`
                            FROM `films`
                            LEFT JOIN `stars_in_films` ON `films`.`id` = `stars_in_films`.`film_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_films`.`star_id`
                            WHERE `films`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `films`.`title`)
                            AND `stars`.`name` LIKE COALESCE(CONCAT('%', :actor_name, '%'), `stars`.`name`)
                            GROUP BY `films`.`id`", ["title" => $title, "actor_name" => $actor_name]);
        return $films_list;
    }

    public static function getByAlphabeticalTitle($title = null, $actor_name = null)
    {
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

    public static function getIdByData(string $title, int $release_year, string $format)
    {
        $film_data = DB::q1("SELECT `id` FROM `films` WHERE `title` = :title AND `release_year` = :release_year AND `format` = :format", ["title" => $title, "release_year" => $release_year, "format" => $format]);
        $film_id = $film_data["id"] ?? null;
        return $film_id;
    }

    public static function create(string $title, int $release_year, string $format)
    {
        $movie_id = DB::qi("INSERT INTO `films` (`title`, `release_year`, `format`) VALUES (:title, :release_year, :format)", ["title" => $title, "release_year" => $release_year, "format" => $format]);
        return $movie_id;
    }

    public static function delete(int $id){
        DB::qi("DELETE FROM `films` WHERE id = :id", ["id" => $id]);
    }

    public static function uploadFromList(array $movies_list)
    {
        foreach ($movies_list as $movie) {

            if (empty($movie)) {
                continue;
            }

            $lines = explode("\n", $movie);

            list($title, $release_year, $format, $stars) = array_map(
                function ($line) {
                    $parts = explode(": ", $line);
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);

                    Validator::isEmpty($key, $value);

                    if ($key === "Format") {
                        Validator::isMovieFormatAcceptable($value);
                    }

                    if ($key === 'Stars') {
                        return explode(', ', $value);
                    }

                    return trim(explode(": ", $line)[1]);
                },
                $lines
            );

            $movie_id = self::getIdByData($title, $release_year, $format);
            if(!$movie_id) {
                $movie_id = self::create($title, $release_year, $format);
            }

            foreach ($stars as $current_star_name) {  
                $actor_id = Stars::getIdByName($current_star_name);
                if(!$actor_id) {
                    $actor_id = Stars::create($current_star_name);

                }

                Stars::addToFilm($actor_id, $movie_id);
            }

        }
    }
}
