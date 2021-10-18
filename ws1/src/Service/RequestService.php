<?php


namespace App\Service;


class RequestService
{
    public function get(string $parameter = '') {
        if($parameter !== '') {
            return $_GET[$parameter] ?? null;
        }
        return $_GET;
    }

    public function post(string $parameter = '') {
        if($parameter !== '') {
            return $_POST[$parameter] ?? null;
        }
        return $_POST;
    }

    public function body(string $parameter = '') {
        try {
            $json = json_decode(file_get_contents('php://input'), true);
        }
        catch (\Exception $exception){
            throw $exception;
        }
        if($parameter !== '') {
            return $json[$parameter] ?? null;
        }
        return $json;
    }

    public function files() {
        return $_FILES;
    }
}