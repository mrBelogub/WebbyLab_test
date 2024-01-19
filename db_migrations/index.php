<?php

require_once "../api/src/Core/sql_shortcuts.php";

const SQL_FILES_FOLDER = "sql_files";

// Отримання списку всіх файлів у папці міграцій
$migrationFiles = glob(SQL_FILES_FOLDER . '/*.sql');

// Перебір усіх файлів та виконання міграцій
foreach ($migrationFiles as $migrationFile) {
    $sql = file_get_contents($migrationFile);
    $queries = explode(';', $sql);

    array_pop($queries);
    
    foreach ($queries as $query) {
        DB::q($query, []);
    }
}

echo "Готово";