<?php
//
// 客户端被强制结束时，造成 socket 不正常关闭
//

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_connect($so, 'localhost', 9999);

echo "Press Ctrl^C to kill process in 10 sec" . PHP_EOL;
sleep(10);

