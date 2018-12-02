<?php
/**
 *
 * Author: abel
 * Email:abel.zhou@hotmail.com
 * Date: 2018/11/30
 * Time: 14:47
 */


//创建websocket服务器对象，监听0.0.0.0:9527端口
$ws = new swoole_websocket_server("0.0.0.0", 9527);
$ws->set(['worker_num' => 4]);
$fds = new swoole_table(1024);
$fds->column("fd", swoole_table::TYPE_INT);
$fds->column("name", swoole_table::TYPE_STRING, 32);
$fds->create();
$ws->fds = $fds;

////监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    //增加链接数
    $fd = $request->fd;
    $ws->fds->set($fd, ["fd" => $fd, "name" => "Client({$fd})"]);
    echo "connected! open{$request->fd}." . PHP_EOL;
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $msg = 'from' . $frame->fd . ":{$frame->data}\n";
    $sourceFd = $ws->fds->get($frame->fd);
    echo $msg;
    foreach ($ws->fds as $fd=>$fd_data) {
        $ws->push($fd, $sourceFd["name"] . ": " . $frame->data);
    }
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    $ws->fds->del($fd);
    echo "client-{$fd} is closed\n";
});

$ws->start();


