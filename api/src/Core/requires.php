<?php

// Файл зв'язування між собою файлів бекенду

require_once 'sql_shortcuts.php'; // Скорочення для SQL запитів
require_once 'validator.php'; // Валідатор

require_once __DIR__.'/../Controllers/Movie.php'; // Функіонал зв'язаний з фільмом
require_once __DIR__.'/../Controllers/Star.php'; // Функіонал зв'язаний з зіркою
require_once __DIR__.'/../Controllers/User.php'; // Функіонал зв'язаний з користувачем