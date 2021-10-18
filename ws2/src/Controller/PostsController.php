<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use App\Service\JwtService;
use App\Service\RequestService;
use App\Service\SocketsService;
use App\Service\UsersService;
use App\Service\ValidatorService;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\SocketServer;

class PostsController
{

    public function postsEdit(int $id){
        header("Content-Type: application/json; charset=UTF-8");

        // Check user authentication
        $userService = new UsersService();
        $user = $userService->getUser();
        if($user === null) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode(['errors' => $userService->getErrors()]);
            return $response;
        }

        $postsRepository = new PostsRepository();
        $postDb = $postsRepository->find('id', $id);

        if(!isset($postDb['success'])) {
            $response['status_code_header'] = 404;
            $response['body'] = json_encode([]);
            return $response;
        }

        if((int)$postDb['success']['user_id'] !== (int)$user['id']) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode(['You do not have access to edit this post!']);
            return $response;
        }

        // Check request body and validate the group required for adding posts
        $data = (new RequestService())->body();
        $data['user_id'] = (int)$user['id'];
        $validator = new ValidatorService();
        $result = $validator->validate('posts_edit', $data);

        if(count($result['errors']) > 0) {
            $response['status_code_header'] = 422;
            $response['body'] = json_encode($result);
            return $response;
        }

        $post = (new PostsRepository())->update($id, $data);

        if(!isset($post['success'])) {
            $response['status_code_header'] = 500;
            $response['body'] = json_encode($post['errors'] ? $post : []);
            return $response;
        }

        $response['status_code_header'] = 200;
        $response['body'] = json_encode([]);
        return $response;
    }

    public function postsDelete(int $id){
        header("Content-Type: application/json; charset=UTF-8");

        // Check user authentication
        $userService = new UsersService();
        $user = $userService->getUser();
        if($user === null) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode(['errors' => $userService->getErrors()]);
            return $response;
        }

        $postsRepository = new PostsRepository();
        $postDb = $postsRepository->find('id', $id);

        if(!isset($postDb['success'])) {
            $response['status_code_header'] = 404;
            $response['body'] = json_encode([]);
            return $response;
        }

        if((int)$postDb['success']['user_id'] !== (int)$user['id']) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode(['You do not have access to delete this post!']);
            return $response;
        }

        $post = (new PostsRepository())->delete($id);

        if(isset($post['errors'])) {
            $response['status_code_header'] = 500;
            $response['body'] = json_encode($post['errors'] ? $post : []);
            return $response;
        }

        $response['status_code_header'] = 200;
        $response['body'] = json_encode([]);
        return $response;
    }

    public function postsNew(){
        header("Content-Type: application/json; charset=UTF-8");

        // Check user authentication
        $userService = new UsersService();
        $user = $userService->getUser();
        if($user === null) {
            $response['status_code_header'] = 403;
            $response['body'] = json_encode(['errors' => $userService->getErrors()]);
            return $response;
        }


        // Check request body and validate the group required for adding posts
        $data = (new RequestService())->body();
        $data['user_id'] = (int)$user['id'];
        $validator = new ValidatorService();
        $result = $validator->validate('posts_add', $data);

        if(count($result['errors']) > 0) {
            $response['status_code_header'] = 422;
            $response['body'] = json_encode($result);
            return $response;
        }

        $post = (new PostsRepository())->create($data);
        if(!isset($post['success'])) {
            $response['status_code_header'] = 500;
            $response['body'] = json_encode([]);
            return $response;
        }

        $response['status_code_header'] = 201;
        $response['body'] = json_encode([]);
        return $response;
    }

    public function postsList(){

        header("Content-Type: application/json; charset=UTF-8");

        // Restrict post by user requested
        $restrictUser = (new RequestService())->get('restrictUser');
        $page = (new RequestService())->get('page') === null ? 1 : (new RequestService())->get('page');
        $limit = (new RequestService())->get('limit') === null ? 10 : (new RequestService())->get('limit');

        if($restrictUser !== null) {
            // Check user authentication
            $userService = new UsersService();
            $user = $userService->getUser();
            if($user === null) {
                $response['status_code_header'] = 403;
                $response['body'] = json_encode(['errors' => $userService->getErrors()]);
                return $response;
            }
            $posts = (new PostsRepository())->getAll($user['id'] ,$page, $limit);
        }
        else {
            $posts = (new PostsRepository())->getAll(null, $page, $limit);
        }

        if(!isset($posts['success'])) {
            $response['status_code_header'] = 500;
            $response['body'] = json_encode([]);
            return $response;
        }

        $response['status_code_header'] = 200;
        $response['body'] = json_encode($posts['success']);
        return $response;
    }

}