<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Храните ссылку на подключение
        $this->clients->attach($conn);
        $currentTime = now();
        echo $currentTime;
        echo " Новый пользователь подключен: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->clients as $client) {
            // Отправьте сообщение всем клиентам, кроме отправителя
            if ($from !== $client) {
                $client->send($msg);
            }
        }
        var_dump($msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Удалите подключение
        $this->clients->detach($conn);
        echo "Пользователь отключен: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Ошибка: {$e->getMessage()}\n";
        $conn->close();
    }
}
