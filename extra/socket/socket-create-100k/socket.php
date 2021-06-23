<?php
// 
// 仅仅是创建 socket，总是成功的
//

$pool = array();

$start_memo = memory_get_usage(); // 记录起始内存用量
$start_time = microtime(); // 记录起始时间

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

$end_time = microtime(); // 记录结束时间
$end_memo = memory_get_usage(); // 记录结束内存用量


echo 'size=' . count($pool) . PHP_EOL;

// 花费大约 0.7 秒
//var_dump($start_time);
//var_dump($end_time);

// 内存增加了约 19 M
//var_dump($start_memo);
//var_dump($end_memo);

