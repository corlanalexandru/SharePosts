<?php


namespace App\Service;


class UsersService
{

    public function checkPassword($password, $repeatPassword) {
        if($password !== $repeatPassword) {
            return 'Your passwords are not equal! ';
        }
        return '';
    }

    public function checkEmail($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid e-mail address! ';
        }
       return '';
    }
}