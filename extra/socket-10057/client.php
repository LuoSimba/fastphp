<?php
// 
// 没有设置地址，也没有连接的情况下，socket 是不能读写的
//

define('TEXT', 'hello');

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

echo 'send' . PHP_EOL;

socket_send($so, TEXT, strlen(TEXT), 0);

echo 'recv' . PHP_EOL;

$n = socket_recv($so, $buf, 4, MSG_WAITALL);

echo 'end' . PHP_EOL;

