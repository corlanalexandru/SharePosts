<?php


namespace App\Service;


class ValidatorService
{

    private const VALIDATION_GROUPS = [
        'users_login' =>
            [
                'username' => ['type' => 'string', 'required' => true, 'denied_chars' => [' ', "'", "/", "\\"], 'min_length' => '5'],
                'password' => ['type' => 'string', 'required' => true, 'denied_chars' => [' ', "'", "/", "\\"], 'min_length' => '5'],
            ],
        'users_register' =>
            [
                'password' => ['type' => 'string', 'required' => true, 'min_length' => '5', 'callback' => ['name' => 'checkPassword', 'class' => 'Users', 'parameters' => ['password', 'repeat_password']]],
                'username' => ['type' => 'string', 'required' => true, 'denied_chars' => [' ', "'", "/", "\\"], 'min_length' => '5', 'callback' => ['name' => 'checkEmail', 'class' => 'Users', 'parameters' => ['username']]],
                'repeat_password' => ['type' => 'string', 'required' => true, 'min_length' => '5'],
                'name' => ['type' => 'string', 'required' => true, 'min_length' => '3'],
            ]
    ];


    public function validate($group, $data): array
    {
        $result['errors'] = [];
        $result['data'] = [];
        if(!isset($this::VALIDATION_GROUPS[$group])) {
            $result['errors'][] = 'Validation groups does not exists! Define one or avoid validation!';
            return $result;
        }

        foreach ($this::VALIDATION_GROUPS[$group] as $key=>$item) {

            if($item['required'] && (!isset($data[$key]) || trim($data[$key]) === '')) {
                $result['errors'][$key] = 'Field '.$key.' is required!';
            }
            elseif(gettype($data[$key]) !== $item['type']) {
                $result['errors'][$key] = 'Field '.$key.' should be of type '.$item['type'].'! ';
            }
            elseif(isset($item['min_length']) && strlen($data[$key]) < $item['min_length']) {
                $result['errors'][$key][] = 'Value of field '.$key.' should be minimum '.$item['min_length'].' characters length! ';
            }
            elseif(isset($item['denied_chars'])) {
                $count = 0;
                str_replace($item['denied_chars'],'', $data[$key], $count);
                if($count) {
                    $result['errors'][$key] ='Value of field '.$key.' should not contain any spaces, slashes or quotes! ';
                }
            }
            if(isset($item['callback'])) {
                $class = 'App\\Service\\' . $item['callback']['class'].'Service';
                $method = $item['callback']['name'];
                if(class_exists($class) && method_exists($class, $method)) {
                    $parameters = [];
                    foreach ($item['callback']['parameters'] as $parameter) {
                        $parameters[] = $data[$parameter];
                    }
                    if(count($parameters) > 0) {
                        $callbackResult = $class::$method(...$parameters);
                    }
                    else {
                        $callbackResult = $class::$method();
                    }
                    if($callbackResult !== '') {
                        $result['errors'][$key][] = $callbackResult;
                    }
                }
            }
        }
        if(count($result['errors']) == 0) {
            $result['data'] = $data;
        }
        else {
            unset($result['data']);
        }

        return $result;
    }

}