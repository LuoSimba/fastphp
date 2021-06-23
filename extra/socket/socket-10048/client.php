<?php
// 
// 创建无限多个 socket 去连服务器
//
// 大量的连接会将端口号耗尽
//
// 由于 TIME_WAIT 的存在，就算正常关闭 socket 也是不行的，
// 在一定的时间内，端口号不能被重用，除非设置
//
//  SO_REUSEADDR
//  SO_REUSEPORT
//


// 记录哪些端口被用过
$map = array();

for ($i = 0; $i < 100000; $i ++)
{
    $so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    // try connect to server
    $bool = socket_connect($so, 'localhost', 9999);
    if ($bool === false)
    {
        echo "connect failed!" . PHP_EOL;
        echo "index = " . $i . PHP_EOL;
        break;
    }

    // get my address info
    $bool2 = socket_getsockname($so, $addr, $port);
    if ($bool2 === false)
    {
        echo 'error getsockname()' . PHP_EOL;
        break;
    }

    if (array_key_exists($port, $map))
    {
        echo 'key exists = ' . $port . PHP_EOL;
        break;
    }

    $map[ $port ] = true;
}

// info
echo PHP_EOL;
echo '---------------' . PHP_EOL;
echo 'map size = ' . count($map) . PHP_EOL;
echo '---------------' . PHP_EOL;

