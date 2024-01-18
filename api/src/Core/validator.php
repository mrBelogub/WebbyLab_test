<?php

class Validator{
    public static function isEmpty($name, $var){
        if(empty($var)){
            throw new Exception($name." is empty!");
        }
    }
}