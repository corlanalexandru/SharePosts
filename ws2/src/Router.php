<?php
namespace App;


class Router
{

    private const ROUTES = [
        'posts/new' => ['controller' => 'Posts', 'route' => 'postsNew', 'methods' => ['POST']],
        'posts/edit/' => ['controller' => 'Posts', 'route' => 'postsEdit', 'methods' => ['PUT'], 'regex' => '/^posts\/edit\/\d+$/'],
        'posts/delete/' => ['controller' => 'Posts', 'route' => 'postsDelete', 'methods' => ['DELETE'], 'regex' => '/^posts\/delete\/\d+$/'],
        'posts/list' => ['controller' => 'Posts', 'route' => 'postsList', 'methods' => ['GET']],
    ];

    public function handle($request)
    {
        echo $this->routes($request)['body'];
    }

    private function routes($request)
    {

        // PREFLIGHT CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
            header('Access-Control-Allow-Headers: Authorization, Content-Type');
            header('Access-Control-Max-Age: 1728000');
            header('Content-Length: 0');
            header('Content-Type: text/plain');
            die();
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PATCH,OPTIONS,GET,POST,PUT,DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $uri = parse_url($request['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('api/', $uri);
        $response['status_code_header'] = 404;
        $response['body'] = null;


        $routes = $this->matchRegex($uri[1]);
        if(count($routes) === 0) {
            http_response_code($response['status_code_header']);
            return $response;
        }

        foreach ($routes as $key => $item) {
            $controller = 'App\\Controller\\' . $item['controller'] . 'Controller';
            $route = $item['route'];
            $uriParams = str_replace($key, '', $uri[1]);
            $parameters = explode('/', $uriParams);

            if ($this->matchMethod($request['REQUEST_METHOD'], $item['methods']) && class_exists($controller) && method_exists($controller, $route)) {
                if(count($parameters) > 0) {
                    $response = $controller::$route(...$parameters);
                }
                else {
                    $response = $controller::$route();
                }
                if (isset($response['status_code_header']) && isset($response['body'])) {
                    http_response_code($response['status_code_header']);
                    return $response;
                }
            }
        }
        http_response_code($response['status_code_header']);
        return $response;
    }

    private function matchRegex($uri): array
    {
        $routes = [];
        foreach ($this::ROUTES as $key => $item) {
            // validate same number of parts
            if(count(explode('/', $key)) === count(explode('/', $uri))) {
                // validate if route has regex
                if(isset($item['regex'])) {
                    if(preg_match($item['regex'], $uri) === 1) {
                        $routes[$key] = $item;
                    }
                }
                else if($key === $uri){
                    $routes[$key] = $item;
                }
            }
        }
        return $routes;
    }

    private function matchMethod(string $requestMethod, array $routeMethods): bool
    {
        return in_array($requestMethod, $routeMethods);
    }

}