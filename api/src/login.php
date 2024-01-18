<?php

$email = $_POST['email'];
$password = $_POST['password'];

Validator::isEmpty("E-mail", $email);
Validator::isEmpty("Пароль", $password);

User::Login($email, $password);