<?php
// 
// 仅仅是创建 socket，总是成功的
//

$pool = array();

for ($i = 0; $i < 100000; $i ++)
{
    $so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if (!is_resource($so))
    {
        echo $i . PHP_EOL;
        die;
    }

    $pool[] = $so;
}

echo 'size=' . count($pool) . PHP_EOL;

