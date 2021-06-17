<?php
// 
// 将两个 socket 绑定到同一个地址
// 后绑定的 socket 会失败
//
// 但是如果将资源及时销毁，地址就会重新可用
//

echo "create socket 1" . PHP_EOL;
$s1 = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
var_dump($s1);

echo "bind address 1" . PHP_EOL;
$b1 = socket_bind($s1, 'localhost', 9999);
if ($b1 === false)
{
    echo "bind address 1 fail" . PHP_EOL;
}

echo "destroy socket 1" . PHP_EOL;
unset($s1);


echo '----------------------------' . PHP_EOL;


echo 'create socket 2' . PHP_EOL;
$s2 = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
var_dump($s2);

echo "bind address 2" . PHP_EOL;
$b2 = socket_bind($s2, 'localhost', 9999);
if ($b2 === false)
{
    echo "bind address 2 fail" . PHP_EOL;
}


