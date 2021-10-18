<?php


namespace App\Service;


use App\Repository\UsersRepository;

class UsersService
{
    private $errors;
    private $jwt;
    public function __construct($errors = [])
    {
        $this->errors = $errors;
        $this->jwt = $this->extractTokenFromHeader();
    }

    public function getUser() {
        try {
            $jwtService = (new JwtService())->decode($this->jwt);
        }
        catch(\Exception $exception) {
            $this->errors[] = $exception->getMessage();
            return null;
        }

        $user = (new UsersRepository())->find('username', $jwtService->username);
        if(!isset($user['success'])) {
            $this->errors = $user['errors'];
        }

        return $user['success'];
    }

    public function getErrors() {
        return $this->errors;
    }

    private function extractTokenFromHeader() {
        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            $this->errors[] = 'Token not found in request';
            return false;
        }
        $jwt = $matches[1];
        if (!$jwt) {
            $this->errors[] = 'No token was able to be extracted from the authorization header';
            return false;
        }
        return $jwt;
    }
}