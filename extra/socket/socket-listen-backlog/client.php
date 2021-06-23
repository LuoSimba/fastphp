<?php

//
// 客户端不停地向服务器发起连接，直到有一个连接失败，
// 那么就说明服务器的队列已经满了。
//


$pool = array();


for ($i = 0; ; $i ++)
{
    $so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    $bool = socket_connect($so, 'localhost', 9999);

    if ($bool === false)
    {
        break;
    }

    $pool[] = $so;
}

echo "count = " . $i . PHP_EOL;

foreach ($pool as $index => $conn)
{
    socket_getsockname($conn, $addr, $port);
    socket_getpeername($conn, $addr2, $port2);

    echo "$index: $addr:$port -> $addr2:$port2" . PHP_EOL;
}

echo "END" . PHP_EOL;

