<?php
// 
// 必然会出现 10061 错误
//
// 由于服务端队列长度只有 5，且服务端根本没有
// 执行 accept 来接受连接，因此排队越来越长，
//
// 当第 6 个连接无法排队的时候，就会立即报错。

for ($i = 0; $i < 10; $i ++)
{
    echo $i . PHP_EOL;

    $so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    // error 10061
    $bool = socket_connect($so, 'localhost', 9999);

    if ($bool === false)
        die;
}

