<?php

namespace App\WebSocket;
// namespace App\Models;
// use App\Auth;
// use App\WebSocket\stdClass
use App\Http\Controllers\SendMessage;
use App\Models\Connection;
use App\Models\Message;
// use App\Models\Chat;
use App\Models\Chat as UserChat;

use App\Models\User;
use Exception;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Header;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Illuminate\Support\Facades\Log;

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $all_clients;
    // private $user_connected;
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
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
        $sessionId = str_replace('%3D', '', Header::parse($conn->httpRequest->getHeader('Cookie'))[0]['laravel_session']);
        $sid = Crypt::decryptString($sessionId);
        $parts = explode('|', $sid);
        $dd = unserialize(file_get_contents(config('session.files') . '/' . $parts[1]));
        $user = User::where('id', $dd['login_web_' . sha1(SessionGuard::class)])->first();
        $user_id = $user->id;

        $user_connections = Connection::where('user_id', $user->id)->first();
        Connection::where('user_id', $user->id)->delete();
        // if ($user_connections == null) {
        $conn = Connection::create([
            'user_id' => $user->id,
            'connection' => $conn->resourceId,
        ]);

        $all_chats = UserChat::where('creator', $user_id)->orWhere('invted', $user_id)->where('chat_started', true)->get();
        $users_for_online_state_update = [];
        foreach ($all_chats as $chat) {
            if ($chat->creator != $chat->invted) {
                echo $chat->creator . PHP_EOL;
                echo $chat->invted . PHP_EOL;
                if ($chat->creator == $user_id && Connection::where('user_id', $chat->invted)->first() != null) {
                    $users_for_online_state_update[] = $chat->invted;
                } else {
                    if (Connection::where('user_id', $chat->creator)->first() != null) {
                        $users_for_online_state_update[] = $chat->creator;
                    }
                }
            }
        }
        $key = array_search($user_id, $users_for_online_state_update);
        if ($key !== false) {
            unset($users_for_online_state_update[$key]);
        }
        // $object = new \stdClass();
        // $object->type = 'update_online_state';
        // $object->user_id = $user_id;

        $msg = new \stdClass();

        // Устанавливаем свойства
        $msg->type = 'update_online_state';
        $msg->user_id = $user_id;
        $msg->is_online = true;
        $msg = json_encode($msg);
        // Для проверки
        // dump($msg);
        dump($users_for_online_state_update);
        foreach ($users_for_online_state_update as $us) {
            // dump($us);
            $connection = Connection::where('user_id', $us)->first();
            // dump($connection);
            if ($connection != null) {
                $connection_addressee = intval($connection->connection);
                // dump($connection_addressee);
                $targetResourceId = $connection_addressee; // Замените на нужный resourceId
                dump($this->all_clients);
                // if (array_search($targetResourceId, $this->all_clients) !== false) {
                $this->all_clients[$targetResourceId]->send($msg);
                // }
                // $user_connections = intval(Connection::where('user_id', $user->id)->first()->connection);
                // $this->all_clients[$user_connections]->send($msg);
            }
        }
        // dd(1);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (json_decode($msg)->type_message == 'message') {
            $sessionId = str_replace('%3D', '', Header::parse($from->httpRequest->getHeader('Cookie'))[0]['laravel_session']);
            $sid = Crypt::decryptString($sessionId);
            $parts = explode('|', $sid);
            $dd = unserialize(file_get_contents(config('session.files') . '/' . $parts[1]));
            $user_id = $dd['login_web_' . sha1(SessionGuard::class)];
            $message = json_decode($msg);
            $MESSAGE = '';
            foreach ($message->message as $mess) {
                $MESSAGE = $MESSAGE . $mess;
            }
            $addressee = $message->addressee;
            $connection = Connection::where('user_id', $addressee)->first();
            $message_table = Message::create([
                'sender_id' => $user_id,
                'addressee' => $addressee,
                'chat_id' => $message->chat_id,
                'message' => json_encode($message->message),
                'key_string' => $message->encrypted_key,
            ]);

            $msg = json_decode($msg);
            $msg->message_id_new = $message_table->id;
            $msg->flag = 'new_message';
            $msg->created_at = $message_table->created_at;
            $msg = json_encode($msg);

            // dump($msg);
            // if ($connection != null) {
            // dd( $connection);

            // $connection_addressee = intval($connection->connection);
            // $targetResourceId = $connection_addressee; // Замените на нужный resourceId

            if ($connection == null) {
                echo 'Клиент не в сети\n';
                // return ('Клиент не в сети\n');
            } else {
                $connection_addressee = intval($connection->connection);
                $targetResourceId = $connection_addressee; // Замените на нужный resourceId
                $this->all_clients[$targetResourceId]->send($msg);
                // dump($msg);
            }

            // Тут мы отпраим пользователю обратно ууже полученный id сообщения вместо guid
            $connection = Connection::where('user_id', $user_id)->first();
            $msg = json_decode($msg);
            // unset($msg->$key);
            $msg->latest_message_id = $message->guid;
            $msg->new_message_id = $message_table->id;
            $msg->flag = 'remove_guid';
            $msg = json_encode($msg);
            if ($connection == null) {
                echo 'Клиент не в сети' . PHP_EOL;
            } else {
                $connection_addressee = intval($connection->connection);
                $targetResourceId = $connection_addressee; // Замените на нужный resourceId
                $this->all_clients[$targetResourceId]->send($msg);
            }
        }
        if (json_decode($msg)->type_message == 'read_sate_update') {
            $data = json_decode($msg);
            $msg = json_decode($msg);

            // dump($data);
            // $msg -> type = 'read_sate_update';
            $message_read = Message::where('message_id', $data->message_id)->update(['isRead' => true]);
            $user_addressee = Message::where('message_id', $data->message_id)->first()->sender_id;
            $msg->chat_id = Message::where('message_id', $data->message_id)->first()->chat_id;
            $connection_addressee = intval(Connection::where('user_id', $user_addressee)->first()->connection);
            $targetResourceId = $connection_addressee;
            $msg = json_encode($msg);
            // dump($this->all_clients[$targetResourceId]->send($msg));
            // $addressee = $message_read->addressee;
            // dump(Message::where('message_id', $data->message_id)->first());
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Удалите подключение
        $this->clients->detach($conn);
        unset($this->all_clients[$conn->resourceId]);
        $sessionId = str_replace('%3D', '', Header::parse($conn->httpRequest->getHeader('Cookie'))[0]['laravel_session']);
        $sid = Crypt::decryptString($sessionId);
        $parts = explode('|', $sid);
        $dd = unserialize(file_get_contents(config('session.files') . '/' . $parts[1]));
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
