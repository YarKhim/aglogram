<?php

namespace App\WebSocket;
// namespace App\Models;
// use App\Auth;
// use App\WebSocket\stdClass
// use App\Http\Controllers\SendMessage;
// use App\Models\Connection;
// use App\Models\Message;
// use App\Models\Chat;
// use App\Models\Chat as UserChat;

// use App\Models\User;
use Exception;
// use GuzzleHttp\Promise\Create;
// use GuzzleHttp\Psr7\Header;
// use Illuminate\Auth\SessionGuard;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Crypt;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Storage;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

// use Illuminate\Support\Facades\Log;

class webrtc implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Храните соединение
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Рассылаем сообщение всем клиентам, кроме отправителя
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Удаляем соединение
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnectedn";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}n";
        $conn->close();
    }
}
