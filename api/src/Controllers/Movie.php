<?php

class Movie
{
    public static function getWithoutSorting($title = null, $star_name = null)
    {
        $movies_list = DB::q("SELECT `movies`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `stars`
                            FROM `movies`
                            LEFT JOIN `stars_in_movies` ON `movies`.`id` = `stars_in_movies`.`movie_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_movies`.`star_id`
                            WHERE `movies`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `movies`.`title`)
                            AND `stars`.`name` LIKE COALESCE(CONCAT('%', :star_name, '%'), `stars`.`name`)
                            GROUP BY `movies`.`id`", ["title" => $title, "star_name" => $star_name]);
        return $movies_list;
    }

    public static function getByAlphabeticalTitle($title = null, $star_name = null)
    {
        $movies_list = DB::q("SELECT `movies`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `stars`
                            FROM `movies`
                            LEFT JOIN `stars_in_movies` ON `movies`.`id` = `stars_in_movies`.`movie_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_movies`.`star_id`
                            WHERE `movies`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `movies`.`title`) 
                            AND `stars`.`name` LIKE COALESCE(CONCAT('%', :star_name, '%'), `stars`.`name`)
                            GROUP BY `movies`.`id`
                            ORDER BY `movies`.`title` ASC", ["title" => $title, "star_name" => $star_name]);

        return $movies_list;
    }

    public static function getIdByData(string $title, int $release_year, string $format)
    {
        $movie_data = DB::q1("SELECT `id` FROM `movies` WHERE `title` = :title AND `release_year` = :release_year AND `format` = :format", ["title" => $title, "release_year" => $release_year, "format" => $format]);
        $movie_id = $movie_data["id"] ?? null;
        return $movie_id;
    }

    public static function create(string $title, int $release_year, string $format)
    {
        $movie_id = DB::qi("INSERT INTO `movies` (`title`, `release_year`, `format`) VALUES (:title, :release_year, :format)", ["title" => $title, "release_year" => $release_year, "format" => $format]);
        return $movie_id;
    }

    public static function delete(int $id){
        DB::qi("DELETE FROM `movies` WHERE id = :id", ["id" => $id]);
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

                    return trim(explode(": ", $line, 2)[1]);
                },
                $lines
            );

            $movie_id = self::getIdByData($title, $release_year, $format);
            if(!$movie_id) {
                $movie_id = self::create($title, $release_year, $format);
            }

            foreach ($stars as $current_star_name) {  
                $trimmed_current_star_name = trim($current_star_name); 
                $star_id = Star::getIdByName($trimmed_current_star_name);
                if(!$star_id) {
                    $star_id = Star::create($current_star_name);

                }

                Star::addToMovie($star_id, $movie_id);
            }

        }
    }
}
