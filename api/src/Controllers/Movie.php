<?php

class Movie
{
    public const SORT_TYPE_ALPHABETICAL = "alphabetical";

    /**
     * Отримати всі фільми
     *
     * @param string $sort_type Тип сортування
     * @param string $title Назва фільму
     * @param string $star_name - Ім'я зірки
     * @return array Масив фільмів
     */
    public static function getAll(string $sort_type = null, string $title = null, string $star_name = null): array
    {

        $movies_list = DB::q("SELECT `movies`.*, GROUP_CONCAT(`stars`.`name` SEPARATOR ', ') AS `stars`
                            FROM `movies`
                            LEFT JOIN `stars_in_movies` ON `movies`.`id` = `stars_in_movies`.`movie_id`
                            LEFT JOIN `stars` ON `stars`.`id` = `stars_in_movies`.`star_id`
                            WHERE `movies`.`title` LIKE COALESCE(CONCAT('%', :title, '%'), `movies`.`title`)
                            AND `stars`.`name` LIKE COALESCE(CONCAT('%', :star_name, '%'), `stars`.`name`)
                            GROUP BY `movies`.`id`", ["title" => $title, "star_name" => $star_name]);

        // Якщо вибрано сортировка по алфавіту
        if($sort_type == self::SORT_TYPE_ALPHABETICAL) {
            $movies_list = self::sortByAlphabetic($movies_list);
        }

        // Повертаємо список фільмів
        return $movies_list;
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

                    // Якщо це зірки - розбиваємо на кожну окрему та додаємо в змінну
                    if ($key === 'Stars') {
                        return explode(', ', $value);
                    }

                    // Додаємо значенния у відповідну змінну
                    return trim($value);
                },
                $lines
            );

            // Перевіряємо, чи існує фільм
            Validator::isMovieExist($title);

            // Перевіряємо, чи підходить формат фільму до допустимих
            Validator::isMovieFormatAcceptable($format, $title);

            // Перевіряємо, чи входить рік виходу в допустимий діапазон
            Validator::isMovieReleaseYearInAcceptableRange($release_year, $title);

            // Cтворюємо фільм та отримуємо його ID
            $movie_id = self::create($title, $release_year, $format);

            // Кожну зірку перевіряємо на наявніть в бд (якщо нема - ствостворюємо) та присвоюємо до фільму
            foreach ($stars as $current_star_name) {
                // Видаляємо зайві пробіли в імені
                $trimmed_current_star_name = trim($current_star_name);

                // Перевіяємо чи нема в імені зірки неприпустимих символів
                Validator::checkStarName($trimmed_current_star_name);

                // Перевіряємо, чи існує зірка в бд та отримуємо її ID
                $star_id = Star::getIdByName($trimmed_current_star_name);
                if(!$star_id) {
                    // Якщо зірки не існує - створюємо та отримуємо її ID
                    $star_id = Star::create($trimmed_current_star_name);
                }

                // Привязуємо зірку до фільму
                Star::addToMovie($star_id, $movie_id);
            }
        }
    }

    /**
     * Сортування масиву по алфавіту
     *
     * @param array $movies_list список фільмів
     * @return array Відсортований список фільмів
     */
    private static function sortByAlphabetic(array $movies_list): array
    {
        // Сортируємо масив по алфавіту
        usort($movies_list, function ($a, $b) {
            return strcmp(strtolower($a['title']), strtolower($b['title']));
        });

        // Проходимо по кожному елементу масиву
        for ($i = 0; $i < count($movies_list) - 1; $i++) {

            // Отримуємо першу літеру поточного та наступного слова
            $current_first_letter = mb_substr($movies_list[$i]['title'], 0, 1, 'UTF-8');
            $next_first_letter = mb_substr($movies_list[$i + 1]['title'], 0, 1, 'UTF-8');

            // Дізнаємося чи співпадають перша літера поточного та наступного слова
            $current_letter_equal_next = strtolower($current_first_letter) == strtolower($next_first_letter);

            // Якщо не співпадають - пропускаємо цю ітерацію циклу
            if(!$current_letter_equal_next) {
                continue;
            }

            // Дізнаємося чи велика перша літера поточного та наступного слова
            $current_is_upper = ctype_upper($current_first_letter);
            $next_is_upper = ctype_upper($next_first_letter);

            // Якщо перша літера поточного не маленька або перша літера наступного слова не велика - пропускаємо цю ітерацію циклу
            if($current_is_upper || !$next_is_upper) {
                continue;
            }

            // Міняємо місцями поточний та наступний елемент масиву
            $temp = $movies_list[$i];
            $movies_list[$i] = $movies_list[$i + 1];
            $movies_list[$i + 1] = $temp;
        }

        return $movies_list;
    }
}
