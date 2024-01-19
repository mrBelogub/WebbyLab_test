<?php

class Movie
{
    /**
     * Отримати всі фільми без сортування
     *
     * @param string $title Назва фільму
     * @param string $star_name - Ім'я зірки
     * @return array Масив фільмів
     */
    public static function getWithoutSorting(string $title = null, string $star_name = null): array
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

    /**
     * Отримати всі фільми з сортуванням в алфавітному порядку по назві
     *
     * @param string $title Назва фільму
     * @param string $star_name - Ім'я зірки
     * @return array Масив фільмів
     */
    public static function getByAlphabeticalTitle(string $title = null, string $star_name = null): array
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

    /**
     * Отримати ID конкретного фільму
     *
     * @param string $title Назва фільму
     * @param integer $release_year Рік випуску
     * @param string $format Формат
     * @return integer|null ID фільму якщо знайшов
     */
    public static function getIdByData(string $title, int $release_year, string $format): ?int
    {
        $movie_data = DB::q1("SELECT `id` FROM `movies` WHERE `title` = :title AND `release_year` = :release_year AND `format` = :format", ["title" => $title, "release_year" => $release_year, "format" => $format]);
        $movie_id = $movie_data["id"] ?? null;
        return $movie_id;
    }

    /**
     * Створити фільм
     *
     * @param string $title Назва фільму
     * @param integer $release_year Рік випуску
     * @param string $format Формат
     * @return integer ID фільму
     */
    public static function create(string $title, int $release_year, string $format): int
    {
        $movie_id = DB::qi("INSERT INTO `movies` (`title`, `release_year`, `format`) VALUES (:title, :release_year, :format)", ["title" => $title, "release_year" => $release_year, "format" => $format]);
        return $movie_id;
    }

    /**
     * Видалити фільм
     *
     * @param integer $id ID фільму
     * @return void
     */
    public static function delete(int $id)
    {
        DB::qi("DELETE FROM `movies` WHERE id = :id", ["id" => $id]);
    }

    /**
     * Завантажити фільми з файлу
     *
     * @param string $movies список фільмів
     * @return void
     */
    public static function uploadFromList(string $movies)
    {
        $movies_list = explode("\n\n", $movies);

        // Перебираємо кожен фільм
        foreach ($movies_list as $movie) {

            // Якщо пуста строка - скіпаємо
            if (empty($movie)) {
                continue;
            }

            // Розбиваємо блок фільму на строки
            $lines = explode("\n", $movie);

            // Перетворюємо отриманні дані на змінні
            list($title, $release_year, $format, $stars) = array_map(
                function ($line) {
                    $parts = explode(": ", $line);// Розбиваємо кожну строку на частки
                    $key = trim($parts[0]); // Отримуємо назву параметру
                    $value = trim($parts[1]); // Отримуємо значення

                    // Перевіряємо чи не пусті ці дані
                    Validator::isEmpty($key, $value);

                    // Якщо це формат - перевіряємо чи допустимий він
                    if ($key === "Format") {
                        Validator::isMovieFormatAcceptable($value);
                    }

                    // Якщо це зірки - розбиваємо на кожну окрему та додаємо в змінну
                    if ($key === 'Stars') {
                        return explode(', ', $value);
                    }

                    // Додаємо значенния у відповідну змінну
                    return trim($value);
                },
                $lines
            );

            // Перевіряємо, чи існує фільм та отримуємо його ID
            $movie_id = self::getIdByData($title, $release_year, $format);
            if(!$movie_id) {
                // Якщо фільму ще не існує в БД - створюємо та отримуємо його ID
                $movie_id = self::create($title, $release_year, $format);
            }

            // Кожну зірку перевіряємо на наявніть в бд (якщо нема - ствостворюємо) та присвоюємо до фільму
            foreach ($stars as $current_star_name) {
                // Перевіряємо, чи існує зірка в бд та отримуємо її ID
                $star_id = Star::getIdByName($current_star_name);
                if(!$star_id) {
                    // Якщо зірки не існує - створюємо та отримуємо її ID
                    $star_id = Star::create($current_star_name);
                }

                // Привязуємо зірку до фільму
                Star::addToMovie($star_id, $movie_id);
            }
        }
    }
}
