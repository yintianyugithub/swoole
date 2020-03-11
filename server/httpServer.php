<?php
$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico'){
        $response->end();
        return;
    }
    var_dump($request->get,$request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("this is test of http server". rand(1000,9999));
});

$http->on('request',function ($request, $response){
    list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
    var_dump($controller,$action);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("this is test of http server". rand(1000,9999));
});

$http->start();
