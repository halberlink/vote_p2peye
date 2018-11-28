<?php
/**
 * TYIM服务端 基于swoole
 * Author: abel
 * Date: 17/7/4
 * Time: 16:21
 */




//创建websocket服务器对象，监听0.0.0.0:9527端口
$ws = new swoole_websocket_server("0.0.0.0", 4000);
$ws->addListener("192.168.5.154", 4100,SWOOLE_SOCK_TCP);
//
////监听WebSocket连接打开事件
//$ws->on('open', function ($ws, $request) {
//    $fd[] = $request->fd;
//    $GLOBALS['fd'][] = $fd;
//    echo "connected! open.".PHP_EOL;
//    //$ws->push($request->fd, "hello, welcome\n");
//});


$ws->on('handshake',function(\swoole_http_request $request,swoole_http_response $response){
//    print_r( $request->header );
//    if( 如果不满足我某些自定义的需求条件，那么返回end输出，返回false，握手失败 ){
//        $response->end();
//        return false;
//    }
    //websocket握手连接算法验证
    if(0===preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#',$request->header['sec-websocket-key']) || 16!==strlen(base64_decode($request->header['sec-websocket-key']))){
        $response->end();
        return false;
    }
    $key=base64_encode(sha1($request->header['sec-websocket-key'].'258EAFA5-E914-47DA-95CA-C5AB0DC85B11',true));
    $headers=array(
        'Upgrade'=>'websocket',
        'Redis'=>'Upgrade',
        'Sec-WebSocket-Accept' =>$key,
        'Sec-WebSocket-Version'=>'13',
//        'Sec-WebSocket-Protocol'=>$request->header['sec-websocket-protocol'],
    );
    foreach($headers as $key=>$val){
        $response->header($key,$val);
    }
    $response->status(101);
    $response->end();
        $conn = new Connection();
    $conn->createConnection($request);
    echo "connected! .".$request->fd.PHP_EOL;
    return true;
});


//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $msg =  'from'.$frame->fd.":{$frame->data}\n";
    echo $msg;
//    print_r($ws->request);


//var_dump($GLOBALS['fd']);
//exit;
//    foreach($GLOBALS['fd'] as $aa){
//        foreach($aa as $i){
//            $ws->push($i,$msg);
//        }
//    }
    // $ws->push($frame->fd, "server: {$frame->data}");
    // $ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    $conn = new Connection();
    $conn->destoryConnection($fd);
    echo "client-{$fd} is closed\n";
});

$ws->start();









class Connection{
    private static $host = "127.0.0.1";
    private static $port = "16379";
    private static $redis = null;

    public function __construct()
        {
        if(is_null(self::$redis)){

            self::$redis = new Redis();
            self::$redis->connect(self::$host, self::$port);

        }
    }


    public function createConnection($request){
        $key = "fd_hash";
        $hkey = $request->fd;
        $hvalue = 1;
        self::$redis->HSET($key,$hkey,$hvalue);
    }

    public function destoryConnection($fd){
        $key = "fd_hash";
        $hkey = $fd;
        self::$redis->HDEL($key,$hkey);
    }


}