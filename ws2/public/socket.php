<?php

use App\Service\SocketsService;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;


require dirname(__DIR__).'/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SocketsService()
        )
    ),
    5005
);

$server->run();