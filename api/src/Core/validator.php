<?php

class Validator{

    private const ACCEPTABLE_FILM_FORMATS = ["VHS", "DVD", "Blu-Ray"];

    public static function isEmpty($name, $var){
        if(empty($var)){
            throw new Exception($name." is empty!");
        }
    }

    /**
     * Перевіряє, чи підходить формат фільму до допустимих
     *
     * @param string $format - Формат фільму
     */
    public static function isMovieFormatAcceptable(string $format){
        if(!in_array($format, self::ACCEPTABLE_FILM_FORMATS)){
            throw new Exception($format." - не є допустимим форматом фільму");
            // P.S. Так, можна було б зробити через ENUM в бд, але якщо припустити що це не просто тестове завдання, і треба буде додавати новий формат фільму - то треба буде змінювати набір значень в БД, хоча простіше це зробити в коді. Більш того, якщо прийдеться якийсь формат закрити для завантаження, і видалити значення в ENUM - видаляться і всі фільми з цим форматом. 
        }
        
    }
}