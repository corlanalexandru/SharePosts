<?php


namespace App\Service;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SocketsService implements MessageComponentInterface
{

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {

        // Store the new connection in $this->clients
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        foreach ($this->clients as $client) {

            if ($from->resourceId == $client->resourceId) {
                continue;
            }

            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

}