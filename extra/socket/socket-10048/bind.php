<?php
// 
// 将两个 socket 绑定到同一个地址
// 后绑定的 socket 会失败
//

echo "create 2 sockets" . PHP_EOL;
$s1 = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$s2 = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

echo "bind address 1" . PHP_EOL;
$b1 = socket_bind($s1, 'localhost', 9999);
if ($b1 === false)
{
    echo "bind address 1 fail" . PHP_EOL;
}

echo "bind address 2" . PHP_EOL;
$b2 = socket_bind($s2, 'localhost', 9999);
if ($b2 === false)
{
    echo "bind address 2 fail" . PHP_EOL;
}

