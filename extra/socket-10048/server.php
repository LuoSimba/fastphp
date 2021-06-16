<?php
// 
// 服务端总是接受连接，直到客户端无法分配
// 足够的端口号
//

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($so, 'localhost', 9999);

socket_listen($so, 5);

while (true) {
    $client = socket_accept($so);

    if ($client === false)
    {
        echo 'error' . PHP_EOL;
        die;
    }
}

