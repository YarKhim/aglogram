<?php

namespace App\WebSocket;

// use App\Auth;

use App\Models\Connection;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Header;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;


class Chat implements MessageComponentInterface
{
    protected $clients;
    // private $user_connected;
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
        // echo $conn->resourceId;
        //dump($conn->httpRequest->getHeaders()["Cookie"]);
        $sessionId = str_replace("%3D", "", Header::parse($conn->httpRequest->getHeader("Cookie"))[0]["laravel_session"]);
        $sid = (Crypt::decryptString($sessionId));
        $parts = explode('|', $sid);

        // dump($sid);
        // dump($parts[1]);
        $dd = unserialize(file_get_contents(config("session.files") . "/" . $parts[1]));

        // dump($dd['login_web_'.sha1(SessionGuard::class)]);
        $user = User::where('id', $dd['login_web_' . sha1(SessionGuard::class)])->first();
        // dump($user->id);
        $user_connections = Connection::where('user_id', $user)->first();
        if ($user_connections == null) {
            $conn =  Connection::create([
                'user_id' => $user->id,
                'connection' => $conn->resourceId,
            ]);
        }

        //
        //dump($conn->httpRequest);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // foreach ($this->clients as $client) {
        //     // Отправьте сообщение всем клиентам, кроме отправителя
        //     if ($from !== $client) {
        //         $client->send($msg);
        //     }
        // }
        // $id_sender = ;
        dump($from);
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
