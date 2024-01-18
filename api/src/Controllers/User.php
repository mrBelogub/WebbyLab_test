<?php

class User
{
    public static function Login($email, $pass)
    {
        $user_data = DB::q1("SELECT * FROM `users` WHERE `email` = ?", [$email]);

        if (!$user_data || !password_verify($pass, $user_data['password_hash'])) {
            header("Location: ../login_error.php");
            exit();
        }

        $_SESSION['user'] = password_hash($email . $pass, PASSWORD_DEFAULT);

        header("Location: ../index.php");
        exit();
    }

    public static function Logout()
    {
        $_SESSION['user'] = "";
        unset($_SESSION);

        session_unset();
        session_destroy();
    }
}
