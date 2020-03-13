<?php

class websocketServer
{
    public $server;

    public function __construct()
    {

        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9502);
        /**
         * 客户端与服务端握手完成后回调
         */
        $this->server->on('open', [$this, 'onOpen']);
        /**
         * 服务器接收客户端的数据帧后回调
         */
        $this->server->on('message', [$this, 'onMessage']);

        /**
         * 客户端关闭连接
         */
        $this->server->on('close', [$this, 'onClose']);
        /**
         *comet方案处理长连接  必须是http请求
         */
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->start();
    }

    /**
     * onOpen
     */
    function onOpen($server, $request)
    {
        var_dump($server);
        echo "server: handshake success with fd{$request->fd}\n";
    }

    /**
     * onMessage
     */
    function onMessage($server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    /**
     * onClose
     */
    function onClose($ser, $fd)
    {
        echo "client {$fd} closed\n";
    }

    /**
     * onRequest
     */
    function onRequest($request,$respose)
    {
        global $server;
        // 接收http请求从get获取message参数的值，给用户推送

        // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        foreach ($server->connections as $fd) {
            $respose->push("hello {$fd}");
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            if ($server->isEstablished($fd)) {
                $server->push($fd, $request->get['message']);
            }
            $respose->header('Content-Type','text/html; charset=utf-8',true);
            $respose->end("你好{$request->server['path_info']},结束请求处理");
        }
    }
}

new websocketServer();
