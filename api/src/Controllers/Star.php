<?php

class Star
{
    public static function create(string $name)
    {
        $star_id = DB::qi("INSERT INTO `stars` (`name`) VALUES (:name)", ["name" => $name]);
        return $star_id;
    }

    public static function getIdByName(string $name)
    {
        $star_data = DB::q1("SELECT `id` FROM `stars` WHERE `name` = :name", ["name" => $name]);
        $star_id = $star_data["id"] ?? null;
        return $star_id;
    }

    private static function isAlreadyInMovie(int $star_id, int $movie_id)
    {
        $movie_data = DB::q1("SELECT `id` FROM `stars_in_movies` WHERE `star_id` = :star_id AND `movie_id` = :movie_id", ["star_id" => $star_id, "movie_id" => $movie_id]);
        return boolval($movie_data);
        // P.S. Це можна було б зробити через INSERT INGNORE в бд, але той все одно інкрементує id, а це не гуд. Можна було б цієї таблиці взагалі вилучити id, але оскільки про це нічого не сказано - я на всяк випадок залишив можливість редагування через веб інтерфейс.
    }

    public static function addToMovie(int $star_id, int $movie_id)
    {
        $is_already_in_movie = self::isAlreadyInMovie($star_id, $movie_id);
        if(!$is_already_in_movie) {
            DB::qi("INSERT INTO `stars_in_movies` (`star_id`, `movie_id`) VALUES (:star_id, :movie_id)", ["star_id" => $star_id, "movie_id" => $movie_id]);
        }
    }
}
