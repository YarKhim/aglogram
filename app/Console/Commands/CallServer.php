<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Call;

class CallServer extends Command
{
    protected $signature = 'web:start';
    protected $description = 'Запустить Web сервер';

    public function handle()
    {
        $server = IoServer::factory(
            new HttpServer(new WsServer(new Call())),
            8889, // Порт
        );

        $this->info('WebSocket сервер запущен на порту 8889');
        $server->run();
    }
}
