<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;

class WebSocketServer extends Command {
    protected $signature = 'websocket:start';
    protected $description = 'Запустить WebSocket сервер';

    public function handle() {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            8888 // Порт
        );

        $this->info("WebSocket сервер запущен на порту 8888");
        $server->run();
    }
}
