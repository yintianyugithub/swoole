<?php

/**
 * 阻塞任务处理
 * Todo: 面向对象方式协程化
 */

$server = new Swoole\Server();

$server->set(['task_worket_num' => 4]);

$server->on('receive', function ($ser, $fd, $from_id, $data) use ($a, $b, $c) {
    var_dump($ser);
});
$server->on('task', function ($ser, $f, $fromId, $data) {
    $ser->finish($data);
});

$server->on('finish', function ($server, $task_id, $data) {
    echo "[{$task_id}] finished :{$data}" . PHP_EOL;
});

$server->start();



