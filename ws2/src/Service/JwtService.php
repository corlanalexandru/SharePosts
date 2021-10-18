<?php


namespace App\Service;


use Firebase\JWT\JWT;

class JwtService
{
    public function generate($payload) {
        $payload['iss'] = 'ws1';
        return JWT::encode($payload, file_get_contents(ROOT.'jwt/private.pem'), 'RS256');
    }

    public function decode($jwt) {
        return JWT::decode($jwt, file_get_contents(ROOT.'/jwt/public.pem'), array('RS256'));
    }

}