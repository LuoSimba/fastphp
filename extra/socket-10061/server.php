<?php
// 
// 服务端监听端口但从不接收连接
//

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($so, 'localhost', 9999);

socket_listen($so, 5);

sleep(600);
