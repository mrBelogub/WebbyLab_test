<?php

// Отримуємо ID із запиту
$id = $_POST["id"] ?? null;

// Перевяємо чи не пустий ID прийшов
Validator::isEmpty("ID фільму", $id);

// Видаляємо фільм
Movie::delete($id);
