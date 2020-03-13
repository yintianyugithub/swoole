<?php

class websocketServer
{
    public $server;

    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9502);
        //客户端与服务端握手完成后回调
        $this->server->on('open', [$this, 'onOpen']);
        //服务器接收客户端的数据帧后回调
        $this->server->on('message', [$this, 'onMessage']);
        //客户端关闭连接
        $this->server->on('close', [$this, 'onClose']);

        //处理异步任务
        $this->server->on('task', [$this, 'onTask']);
        //任务完成回调
        $this->server->on('finish', [$this, 'onFinish']);
        //投递任务
        $this->server->on('receive', [$this, 'onReceive']);

        //comet方案处理长连接  必须是http请求
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->start();
    }

    /**
     * @param $server
     * @param $request
     * User: itianyu
     * Date: 2020/3/13 16:47
     */
    function onOpen($server, $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    /**
     * @param $server
     * @param $frame
     * User: itianyu
     * Date: 2020/3/13 16:47
     */
    function onMessage($server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    /**
     * @param $ser
     * @param $fd
     * User: itianyu
     * Date: 2020/3/13 16:47
     */
    function onClose($ser, $fd)
    {
        echo "client {$fd} closed\n";
    }

    /**
     * @param $request
     * @param $respose
     * User: itianyu
     * Date: 2020/3/13 16:46
     */
    function onRequest($request, $respose)
    {
        foreach ($this->server->connections as $fd) {
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            if ($this->server->isEstablished($fd) && $request->server['request_uri'] != '/favicon.ico') {
                $respose->header('Content-Type', 'text/html; charset=utf-8', true);
//                $this->server->push($fd, $request->get['message']);
                $respose->end("你好{$request->server['path_info']},结束请求处理");
            }
        }
    }

    function onReceive($server, $fd, $fromId, $data)
    {
        $server->task();
    }

    function onTask($server, $taskId, $fromId, $data)
    {
        echo 'this is task'.$taskId;
    }

    function onFinish ($server,$taskId,$data)
    {
        echo "AsyncTask[$taskId] Finish: $data".PHP_EOL;
    }
}

new websocketServer();
