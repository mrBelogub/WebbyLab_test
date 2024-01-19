<?php

class User
{
    /**
     * Спроба авторизувати користувача та редірект на відповідну сторінку
     *
     * @param string $email E-mail
     * @param string $pass пароль
     * @return void
     */
    public static function Login(string $email, string $pass)
    {
        // Отримуємо дані користувача по E-mail
        $user_data = DB::q1("SELECT * FROM `users` WHERE `email` = ?", [$email]);

        // Якщо користувача з таким E-mail не знайдено АБО пароль не підходить до хеша - перекидуємо на сторінку з помилкою авторизациії
        if (!$user_data || !password_verify($pass, $user_data['password_hash'])) {
            header("Location: ../login_error.php");
            exit();
        }

        // Якщо ж все добре - записуємо в сессію унікальну строку
        $_SESSION['user'] = password_hash($email . $pass, PASSWORD_DEFAULT);

        // Та перекидуємо на головну сторінку
        header("Location: ../index.php");
        exit();
    }

    /**
     * Деавторизаія користувача
     *
     * @return void
     */
    public static function Logout()
    {
        // Очищуємо, на всяк випадок видаляємо та завершуємо сессію
        $_SESSION['user'] = "";
        unset($_SESSION);

        session_unset();
        session_destroy();

        // Перекидуємо на сторінку авторизациії
        header("Location: ../login.php");
        exit();
    }
}
