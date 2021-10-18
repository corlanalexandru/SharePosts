<?php


namespace App\Repository;


use App\Database;

class UsersRepository
{
    private $db;
    public function __construct()
    {
        $this->db = (new Database())->connect();

    }

    public function find($field, $value)
    {
        $response = [];
        $query = "
              SELECT
                  id, name, username, password, created_at, last_login_at
              FROM
                  users
              WHERE ".$field." = :fieldValue;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('fieldValue' => $value));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if($result) {
                $response['success'] = $result;
            }
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }
        return $response;
    }

}