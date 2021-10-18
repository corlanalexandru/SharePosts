<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Service\JwtService;
use App\Service\RequestService;
use App\Service\ValidatorService;

class SecurityController
{

    public function login(){
        header("Content-Type: application/json; charset=UTF-8");
        $data = (new RequestService())->body();
        $validator = new ValidatorService();
        $result = $validator->validate('users_login', $data);
        if(count($result['errors']) > 0) {
            $response['status_code_header'] = 422;
            $response['body'] = json_encode($result);
            return $response;
        }

        $usersRepository = new UsersRepository();
        $user = $usersRepository->login($result['data']['username'], $result['data']['password']);

        if(!isset($user['success'])) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode($user);
            return $response;
        }

        $jwt = new JwtService();

        $expiresAt = (new \DateTime())->modify('+1 day');
        $payload = array (
            "exp" => $expiresAt->getTimestamp(),
            "name" => $user['success']['name'],
            "username" => $user['success']['username']
        );

        $response['status_code_header'] = 200;
        $response['body'] = json_encode([
            'jwt' => $jwt->generate($payload),
            'expiresAt' => $expiresAt->format('Y-m-d H:i:s'),
            'user' => ['name' => $user['success']['name'],  "username" => $user['success']['username']]
        ]);
        return $response;
    }

    public function register(){
        header("Content-Type: application/json; charset=UTF-8");
        $data = (new RequestService())->body();
        $validator = new ValidatorService();
        $result = $validator->validate('users_register', $data);

        if(count($result['errors']) > 0) {
            $response['status_code_header'] = 422;
            $response['body'] = json_encode($result);
            return $response;
        }

        $usersRepository = new UsersRepository();
        $user = $usersRepository->create($result['data']);

        if(!isset($user['success'])) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode($user);
            return $response;
        }

        $usersRepository = new UsersRepository();
        $user = $usersRepository->find('username',trim($result['data']['username']));
        if(!isset($user['success'])) {
            $response['status_code_header'] = 404;
            $response['body'] = json_encode($user);
            return $response;
        }


        $jwt = new JwtService();
        $expiresAt = (new \DateTime())->modify('+1 day');
        $payload = array (
            "exp" => $expiresAt->getTimestamp(),
            "name" => $user['success']['name'],
            "username" => $user['success']['username']
        );

        $response['status_code_header'] = 200;
        $response['body'] = json_encode([
            'jwt' => $jwt->generate($payload),
            'expiresAt' => $expiresAt->format('Y-m-d H:i:s'),
            'user' => ['name' => $user['success']['name'],  "username" => $user['success']['username']]
        ]);
        return $response;
    }

}