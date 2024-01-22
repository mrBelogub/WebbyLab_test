<?php

class Validator
{
    private const ACCEPTABLE_MOVIES_FORMATS = ["VHS", "DVD", "Blu-Ray"]; // Список допустимих форматів фільмів
    private const MIN_RELEASE_YEAR = 1900; // Мінімально допустимий рік

    /**
     * Перевірка чи пуста змінна
     *
     * @param string $name Назва
     * @param mixed $var Змінна
     * @return boolean Чи пуста змінна
     */
    public static function isEmpty(string $name, $var)
    {
        if(empty($var)) {
            throw new Exception($name . " is empty!");
        }
    }

    /**
     * Перевірка, чи підходить формат фільму до допустимих
     *
     * @param string $format - Формат фільму
     * @param string $title Назва фільму
     * @throws Exception Помилка у разі якщо формат фільму не підходить до допустимих
     */
    public static function isMovieFormatAcceptable(string $format, string $title)
    {

        if(!in_array($format, self::ACCEPTABLE_MOVIES_FORMATS)) {
            throw new Exception("У фільму ".$title." дата виходу (".$format . ") - не є допустимим форматом!");
            // P.S. Так, можна було б зробити через ENUM в бд, але якщо припустити що це не просто тестове завдання, і треба буде додавати новий формат фільму - то треба буде змінювати набір значень в БД, хоча простіше це зробити в коді. Більш того, якщо прийдеться якийсь формат закрити для завантаження, і видалити значення в ENUM - видаляться і всі фільми з цим форматом.
        }

    }

    /**
     * Перевірка, чи входить рік виходу в допустимий діапазон
     *
     * @param integer $release_year Рік випуску
     * @param string $title Назва фільму
     * @throws Exception Помилка у разі якщо рік виходу не входить в допустимий діапазон
     */
    public static function isMovieReleaseYearInAcceptableRange(int $release_year, string $title){
        $current_year = date("Y", time());

        $is_in_acceptable_range = $release_year > self::MIN_RELEASE_YEAR && $release_year <= $current_year;

        if(!$is_in_acceptable_range){
            throw new Exception("У фільму ".$title." дата виходу (".$release_year.") не входить в допустимий діапазон!");
        }
    }

    /**
     * Перевірка чи є фільм з такою назвою
     *
     * @param string $title Назва фільму
     * @throws Exception Помилка у разі якщо фільм з такою назвою вже є
     */
    public static function isMovieExist(string $title)
    {
        $movie_data = DB::q1("SELECT `id` FROM `movies` WHERE `title` = :title", ["title" => $title]);
        if(!empty($movie_data)){
            throw new Exception("Фільм ".$title." вже є в базі!");
        }

    }
}
