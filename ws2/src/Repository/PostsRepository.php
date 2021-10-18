<?php


namespace App\Repository;


use App\Database;

class PostsRepository
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();

    }

    public function create($data)
    {

        $response = [];
        $query = "
          INSERT INTO posts
              (user_id, title, content, created_at)
          VALUES
              (:user_id, :title, :content, :created_at);
        ";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array(
                'user_id' => stripslashes(trim($data['user_id'])),
                'title' => stripslashes(trim($data['title'])),
                'content' => stripslashes(trim($data['content'])),
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ));
            if ($statement->rowCount() > 0) {
                $response['success'] = true;
            }
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
                  user_id, title, content, created_at
              FROM
                  posts
              WHERE " . $field . " = :fieldValue;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('fieldValue' => $value));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if ($result) {
                $response['success'] = $result;
            }
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }
        return $response;
    }

    public function update(int $id, array $data)
    {
        $response = [];
        $updateFields = ['title', 'content'];
        $queryParams = [];
        $queryParamsArr = ['id' => $id];

        foreach ($updateFields as $field) {
            if (isset($data[$field])) {
                $queryParams[] = $field . " = :" . $field;
                $queryParamsArr[$field] = stripslashes(trim($data[$field]));
            }
        }

        if (count($queryParams) > 0) {
            $query = "
              UPDATE posts
              SET " . implode(',', $queryParams) . "
              WHERE id = :id;
            ";

            try {
                $statement = $this->db->prepare($query);
                $statement->execute($queryParamsArr);
                $response['success'] = true;
            } catch (\PDOException $e) {
                $response['errors'][] = $e->getMessage();
            }
        } else {
            $response['errors'][] = 'At least one parameter of ' . implode(', ', $updateFields) . ' is required for update!';
        }

        return $response;
    }

    public function delete(int $id)
    {
        $response = [];
        $query = "
              DELETE FROM posts
              WHERE id = :id;
            ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $id));
            $statement->rowCount();
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }
        return $response;
    }

    public function getAll($userId = null, $page = 1, $limit = 10)
    {
        $response = [];
        $userQuery = '';
        if($userId !== null) {
            $userQuery = ' where p.user_id='.$userId;
        }
        $offset = ($page-1)*$limit;
        $query = "
          SELECT
              p.id, p.title, p.content, u.name as userName,u.username as userUsername, p.created_at
          FROM
              posts p
          left join Users u on u.id = p.user_id ".$userQuery." order by created_at DESC LIMIT ".$limit." OFFSET ".$offset.";
        ";

        try {
            $statement = $this->db->query($query);
            $response['success']['data'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }

        $count = $this->count($userId);
        $response['success']['total'] = isset($count['errors']) ? count($response['success']['data']) : (int)$count;

        return $response;
    }

    public function count($userId = null) {
        $response = [];
        $userQuery = '';
        if($userId !== null) {
            $userQuery = ' where p.user_id='.$userId;
        }
        $query = "
              SELECT
                  count(distinct (p.id)) as total
              FROM
                  posts p
              left join Users u on u.id = p.user_id ".$userQuery.";
            ";

        try {
            $statement = $this->db->query($query);
            $response = $statement->fetchColumn();
        } catch (\PDOException $e) {
            $response['errors'][] = $e->getMessage();
        }
        return $response;
    }
}