<?php

namespace App\WebSocket;

// use App\Auth;

use App\Http\Controllers\SendMessage;
use App\Models\Connection;
use App\Models\Message;
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
    protected $all_clients;
    // private $user_connected;
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }
    // public function a($clientId, $message)
    // {

    // }
    public function onOpen(ConnectionInterface $conn)
    {
        // Храните ссылку на подключение
        // $this->clients[$conn->resourceId] = $conn;

        $this->clients->attach($conn);

        $this->all_clients[$conn->resourceId] = $conn;

        $currentTime = now();
        echo $currentTime;
        echo " Новый пользователь подключен: {$conn->resourceId}\n";
        // dump($this->all_clients[$conn->resourceId]->resourceId);
        // dump($this->all_clients);
        // dump($conn->resourceId);
        $sessionId = str_replace("%3D", "", Header::parse($conn->httpRequest->getHeader("Cookie"))[0]["laravel_session"]);
        $sid = (Crypt::decryptString($sessionId));
        $parts = explode('|', $sid);
        $dd = unserialize(file_get_contents(config("session.files") . "/" . $parts[1]));
        $user = User::where('id', $dd['login_web_' . sha1(SessionGuard::class)])->first();
        $user_connections = Connection::where('user_id', $user->id)->first();
        Connection::where('user_id', $user->id)->delete();
        // if ($user_connections == null) {
        $conn =  Connection::create([
            'user_id' => $user->id,
            'connection' => $conn->resourceId,
        ]);
        // }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // foreach ($this->clients as $client) {
        // Отправьте сообщение всем клиентам, кроме отправителя
        // if ($from !== $client) {
        //     // $client->send($msg);
        // }
        // dump($client);
        // }
        $sessionId = str_replace("%3D", "", Header::parse($from->httpRequest->getHeader("Cookie"))[0]["laravel_session"]);
        $sid = (Crypt::decryptString($sessionId));
        $parts = explode('|', $sid);
        $dd = unserialize(file_get_contents(config("session.files") . "/" . $parts[1]));
        $user_id = $dd['login_web_' . sha1(SessionGuard::class)];
        // dump()


        $message = json_decode($msg);
        $MESSAGE = '';
        foreach ($message->message as $mess) {
            $MESSAGE  = $MESSAGE . $mess;
        }
        $addressee = $message->addressee;
        $connection = Connection::where('user_id', $addressee)->first();


        $message_table = Message::create([
            'sender_id' => $user_id,
            'addressee' => $addressee,
            'chat_id' => $message->chat_id,
            'message' => $MESSAGE,
            'key_string' => $message->encrypted_key,
        ]);
        if ($connection != null) {
            $connection_addressee = intval($connection->connection);
            $targetResourceId = $connection_addressee; // Замените на нужный resourceId
            // dump($targetResourceId);
            // dump($this->all_clients[$targetResourceId]);
            if ($this->all_clients[$targetResourceId] != null) {
                $this->all_clients[$targetResourceId]->send($msg);
                dump('message sent');
            }
        }
        else{
            echo "Пользователь не в сети сообщение отправленно только в бд";
        }


        // foreach ($this->all_clients as $client) {
        //     // dump($client->resourceId);
        //     if ($client->resourceId === $targetResourceId) {
        //         $client->send("Сообщение для клиента: $msg");
        //         dump('message sent');
        //         break; // Выход из цикла после отправки
        //     }
        // }
        // dump($from->resourceId);
        // $this->sendMessageToClient($from, 123);
        // dump('Адресат: ' . $message->addressee . ', Отправитель: ' . $user_id);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Удалите подключение
        $this->clients->detach($conn);
        unset($this->all_clients[$conn->resourceId]);
        $sessionId = str_replace("%3D", "", Header::parse($conn->httpRequest->getHeader("Cookie"))[0]["laravel_session"]);
        $sid = (Crypt::decryptString($sessionId));
        $parts = explode('|', $sid);
        $dd = unserialize(file_get_contents(config("session.files") . "/" . $parts[1]));
        $user = User::where('id', $dd['login_web_' . sha1(SessionGuard::class)])->first();
        $user_id = $user->id;
        Connection::where('user_id', $user_id)->delete();
        echo "Пользователь отключен: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Ошибка: {$e->getMessage()}\n";

        $conn->close();
    }
    public function sendMessageToClient($clientId, $message)
    {
        // dump($clientId);
        // dump($message);
        // dump($clientId);
        // dump($this->clients[$clientId]);

        if (isset($this->clients[$clientId])) {
            $this->clients[$clientId]->send($message);
        }
    }
}
