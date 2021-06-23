<?php
//
// 服务端先发呆 20 秒钟，再处理连接。
//
// 在这 20 秒内，客户端的连接不断过来，最终
// 填满队列，而新的连接无法加入队伍，就失败了。
//
// 当服务端醒来的时候，处理的都是 20 秒前在队列
// 中排队的那些连接。
//
// windows 10: 200 (backlog=1000)
//               5 (backlog=5)
//               1 (backlog=0)

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($so, 'localhost', 9999);
socket_listen($so, 0);
// listen() 可以再次调用，虽然不会报错，但是也不会起作用
//socket_listen($so, 200);

$pool = array();

// 延迟接受连接
sleep(20);

echo "socket_accept() start ..." . PHP_EOL;

for ($i = 0; ; $i ++)
{
    $cli = socket_accept($so);
    $pool[] = $cli;

    socket_getsockname($cli, $addr, $port);
    socket_getpeername($cli, $addr2, $port2);

    echo "$i: $addr:$port -> $addr2:$port2" . PHP_EOL;
}


