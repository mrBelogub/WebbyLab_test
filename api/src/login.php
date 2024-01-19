<?php
// Отримуємо необхідні параметри
$email = $_POST['email'];
$password = $_POST['password'];

// Перевіяємо чи нема випадково пустого значення
Validator::isEmpty("E-mail", $email);
Validator::isEmpty("Пароль", $password);

// Намагаємося авторизувати користувача
User::Login($email, $password);
