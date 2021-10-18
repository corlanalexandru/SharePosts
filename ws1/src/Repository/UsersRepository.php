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
    public function create($data) {

        $response = [];
        $query = "
          INSERT INTO users
              (name, username, password, created_at, last_login_at)
          VALUES
              (:name, :username, :password, :created_at, :last_login_at);
        ";
        if(isset($this->find('username',$data['username'])['success'])) {
            $response['errors']['username'][] = 'Username is already taken!';
            return $response;
        }
        try {
            $statement = $this->db->prepare($query);
            $result = $statement->execute(array(
                'name' => stripslashes(trim($data['name'])),
                'username'  => stripslashes(trim($data['username'])),
                'password' => password_hash(stripslashes(trim($data['password'])),PASSWORD_DEFAULT),
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                'last_login_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ));
            $response['success'] = $result;
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }
        return $response;
    }

    public function find($field, $value)
    {
        $response = [];
        $query = "
              SELECT
                  name, username, password, created_at, last_login_at
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

    public function login($username, $password) {
        $response = [];
        $query = "
              SELECT
                  name, username, password, created_at, last_login_at
              FROM
                  users
              WHERE username = :username;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('username' => $username));

            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if($result && password_verify($password, $result['password'])) {
                $response['success'] = $result;
            }
            else {
                $response['errors'][] = 'Invalid credentials!';
            }
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }
        return $response;
    }

}