<?php

class websocketServer
{
    public $server;

    public function __construct()
    {

        $this->server = new Swoole\WebSocket\Server('0.0.0.0',9502);
        /**
         * 客户端与服务端握手完成后回调
         */
        $this->server->on('open','myOnOpen');

        /**
         * 服务器接收客户端的数据帧后回调
         */
        $this->server->on('message', function ($server, $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            $server->push($frame->fd, "this is server");
        });

        /**
         * 客户端关闭连接
         */
        $this->server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });
        
        $this->server->on('request', function ($request, $response) {
            // 接收http请求从get获取message参数的值，给用户推送
            var_dump($this->server->get);
            // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($this->server->connections as $fd) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($this->server->isEstablished($fd)) {
                    $this->server->push($fd, $request->get['message']);
                }
            }
        });
        $this->server->start();
    }

    /**
     * onOpen
     */
    function myOnOpen ($server, $request) {
        echo "server: handshake success with fd{$request->fd}\n";
    }
}

new websocketServer();
