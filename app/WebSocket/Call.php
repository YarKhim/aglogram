<?php
namespace App\WebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Exception;
use SplObjectStorage;
class Call implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Храните соединение
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Обработка сообщений от клиентов
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Удалите соединение
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e) {
        $conn->close();
    }
}

?>
