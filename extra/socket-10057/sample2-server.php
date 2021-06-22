<?php

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($so, 'localhost', 9999);
socket_listen($so, 5);


$cli = socket_accept($so);

// 尝试获取监听端口的 socket 的远程地址
// socket error 10057
socket_getpeername($so, $peerAddr, $peerPort);
socket_getsockname($so, $localAddr, $localPort);

var_dump($peerAddr);
var_dump($peerPort);

var_dump($localAddr);
var_dump($localPort);

