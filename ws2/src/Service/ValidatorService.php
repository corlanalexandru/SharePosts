<?php


namespace App\Service;


class ValidatorService
{

    private const VALIDATION_GROUPS = [
        'posts_add' =>
            [
                'title' => ['type' => 'string', 'required' => true, 'denied_chars' => ["/", "\\"], 'min_length' => '5'],
                'content' => ['type' => 'string', 'required' => true, 'denied_chars' => ["/", "\\"], 'min_length' => '5'],
                'user_id' => ['type' => 'integer', 'required' => true, 'denied_chars' => [' ', "'", "/", "\\"]],
            ],
        'posts_edit' =>
            [
                'title' => ['type' => 'string', 'required' => false, 'denied_chars' => ["/", "\\"], 'min_length' => '5'],
                'content' => ['type' => 'string', 'required' => false, 'denied_chars' => ["/", "\\"], 'min_length' => '5'],
            ],
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
            elseif(isset($data[$key]) && gettype($data[$key]) !== $item['type']) {
                $result['errors'][$key] = 'Field '.$key.' should be of type '.$item['type'].'!';
            }
            elseif(isset($data[$key]) && isset($item['min_length']) && strlen($data[$key]) < $item['min_length']) {
                $result['errors'][$key] = 'Value of field '.$key.' should be minimum '.$item['min_length'].' characters length!';
            }
            elseif(isset($data[$key]) && isset($item['denied_chars'])) {
                $count = 0;
                str_replace($item['denied_chars'],'', $data[$key], $count);
                if($count) {
                    $spaces = in_array(' ',$item['denied_chars']) ? 'spaces ' : '';
                    $result['errors'][$key] ='Value of field '.$key.' should not contain any '.$spaces.'slashes or quotes!';
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