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
use Illuminate\Support\Facades\Storage;

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
    public function onOpen(ConnectionInterface $conn)
    {
        // Храните ссылку на подключение
        // $this->clients[$conn->resourceId] = $conn;
        $this->clients->attach($conn);

        $this->all_clients[$conn->resourceId] = $conn;

        $currentTime = now();
        echo $currentTime;
        echo " Новый пользователь подключен: {$conn->resourceId}\n";
        $sessionId = str_replace('%3D', '', Header::parse($conn->httpRequest->getHeader('Cookie'))[0]['laravel_session']);
        $sid = Crypt::decryptString($sessionId);
        $parts = explode('|', $sid);
        $dd = unserialize(file_get_contents(config('session.files') . '/' . $parts[1]));
        $user = User::where('id', $dd['login_web_' . sha1(SessionGuard::class)])->first();
        $user_id = $user->id;
        $user->isOnline = true;
        $user->save();
        $user_connections = Connection::where('user_id', $user->id)->first();
        Connection::where('user_id', $user->id)->delete();
        $conn = Connection::create([
            'user_id' => $user->id,
            'connection' => $conn->resourceId,
        ]);
        //рассылаем людям к оторыми у него есть чат сообщение о том что он вошёл в сеть и обновляем данеые в бд
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

        $msg = new \stdClass();

        // Устанавливаем свойства
        $msg->type = 'update_online_state';
        $msg->user_id = $user_id;
        $msg->is_online = true;
        $msg = json_encode($msg);
        foreach ($users_for_online_state_update as $us) {
            $connection = Connection::where('user_id', $us)->first();
            if ($connection != null) {
                $connection_addressee = intval($connection->connection);
                $targetResourceId = $connection_addressee; // Замените на нужный resourceId
                $this->all_clients[$targetResourceId]->send($msg);
            }
        }
        // dd(1);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg_decode = json_decode($msg);
        if ($msg_decode->type_message == 'message') {
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
            $msg = json_decode($msg);
            if ($message->message_data == 'file') {
                // echo 1;
                $hash = hash('sha256', microtime());
                $guid_file_name = sprintf('%s-%s-%s-%s-%s', substr($hash, 0, 8), substr($hash, 8, 4), substr($hash, 12, 4), substr($hash, 16, 4), substr($hash, 20, 12));
                Storage::disk('public')->put('photos_users/' . $guid_file_name . '.txt', json_encode($message->message));

                $url = Storage::url('photos_users/' . $guid_file_name . '.txt');
                $message_table = Message::create([
                    'sender_id' => $user_id,
                    'addressee' => $addressee,
                    'chat_id' => $message->chat_id,
                    'message' => $url,
                    'key_string' => $message->encrypted_key,
                    'type_message' => $message->message_data,
                    'label' => json_encode($message->label),
                ]);
                $msg->message_url = $url;
            }
            if ($message->message_data == 'text') {
                $message_table = Message::create([
                    'sender_id' => $user_id,
                    'addressee' => $addressee,
                    'chat_id' => $message->chat_id,
                    'message' => json_encode($message->message),
                    'key_string' => $message->encrypted_key,
                    'type_message' => $message->message_data,
                ]);
            }

            $msg->message_id_new = $message_table->id;
            $msg->flag = 'new_message';
            $msg->created_at = $message_table->created_at;
            $msg = json_encode($msg);
            if ($connection == null) {
                echo 'Клиент не в сети\n';
            } else {
                $connection_addressee = intval($connection->connection);
                $targetResourceId = $connection_addressee; // Замените на нужный resourceId
                $this->all_clients[$targetResourceId]->send($msg);
            }
            // Тут мы отпраим пользователю обратно ууже полученный id сообщения вместо guid
            $connection = Connection::where('user_id', $user_id)->first();
            $msg = json_decode($msg);
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
        if ($msg_decode->type_message == 'read_sate_update') {
            $data = json_decode($msg);
            $msg = json_decode($msg);
            $message_read = Message::where('message_id', $data->message_id)->update(['isRead' => true]);
            $user_addressee = Message::where('message_id', $data->message_id)->first()->sender_id;
            if (User::where('id', $user_addressee)->first()->isOnline) {
                $msg->chat_id = Message::where('message_id', $data->message_id)->first()->chat_id;
                $connection_addressee = intval(Connection::where('user_id', $user_addressee)->first()->connection);
                $targetResourceId = $connection_addressee;
                $msg = json_encode($msg);
                $this->all_clients[$targetResourceId]->send($msg);
            }
        }
        if ($msg_decode->type_message == 'typing') {
            $currentTime = now();
            $msg = json_decode($msg);
            $user = $msg->user_id;
            echo $currentTime;
            echo ' Пользователь: ' . $msg->user_id . ' - Печатает' . PHP_EOL;
            $chat = UserChat::where('id', $msg->chat_id)->first();
            if ($chat->creator != $chat->invted) {
                $user_1 = $chat->creator;
                $user_2 = $chat->invted;
                $connection_1 = Connection::where('user_id', $user_1)->first();
                $connection_2 = Connection::where('user_id', $user_2)->first();
                if ($user_1 == $user) {
                    $msg->type = 'is_typing';
                    if ($connection_2 != null) {
                        $connection_addressee = $connection_2->connection;
                        $targetResourceId = $connection_addressee;
                        $msg = json_encode($msg);
                        $this->all_clients[$targetResourceId]->send($msg);
                    }
                }
                if ($user_2 == $user) {
                    //send to user_1
                    // $user_addr = $user_1;
                    $msg->type = 'is_typing';

                    if ($connection_1 != null) {
                        $connection_addressee = $connection_1->connection;
                        $targetResourceId = $connection_addressee;
                        $msg = json_encode($msg);
                        $this->all_clients[$targetResourceId]->send($msg);
                    }
                }
            }
            return;
        }
        if ($msg_decode->type_message == 'isnt_typing') {
            $currentTime = now();
            $msg = json_decode($msg);
            $user = $msg->user_id;
            echo $currentTime;
            echo ' Пользователь: ' . $msg->user_id . ' - Не печатает' . PHP_EOL;
            $chat = UserChat::where('id', $msg->chat_id)->first();
            if ($chat->creator != $chat->invted) {
                $user_1 = $chat->creator;
                $user_2 = $chat->invted;
                $connection_1 = Connection::where('user_id', $user_1)->first();
                $connection_2 = Connection::where('user_id', $user_2)->first();
                if ($user_1 == $user) {
                    $msg->type = 'isnt_typing';
                    if ($connection_2 != null) {
                        $connection_addressee = $connection_2->connection;
                        $targetResourceId = $connection_addressee;
                        $msg = json_encode($msg);
                        $this->all_clients[$targetResourceId]->send($msg);
                    }
                }
                if ($user_2 == $user) {
                    $msg->type = 'isnt_typing';
                    dump($connection_1);
                    if ($connection_1 != null) {
                        $connection_addressee = $connection_1->connection;
                        $targetResourceId = $connection_addressee;
                        $msg = json_encode($msg);
                        $this->all_clients[$targetResourceId]->send($msg);
                    }
                }
            }
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
        //рассылаем людям к оторыми у него есть чат сообщение о том что он вышел из сети и обновляем данеые в бд
        $currentTime = now();
        echo $currentTime;
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
        $msg = new \stdClass();

        // Устанавливаем свойства
        $msg->type = 'update_online_state';
        $msg->user_id = $user_id;
        $msg->is_online = false;
        $msg = json_encode($msg);
        foreach ($users_for_online_state_update as $us) {
            $connection = Connection::where('user_id', $us)->first();
            if ($connection != null) {
                $connection_addressee = intval($connection->connection);
                $targetResourceId = $connection_addressee; // Замените на нужный resourceId
                $this->all_clients[$targetResourceId]->send($msg);
            }
        }
        $user->isOnline = false;
        $user->save();
        echo " Пользователь отключен: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Ошибка: {$e->getMessage()}\n";

        $conn->close();
    }
    public function sendMessageToClient($clientId, $message)
    {
        if (isset($this->clients[$clientId])) {
            $this->clients[$clientId]->send($message);
        }
    }
}
