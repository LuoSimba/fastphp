<?php

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($so, 'localhost', 9999);

socket_listen($so, 5);

$client = socket_accept($so);

$n = socket_recv($client, $buf, 100, 0);

var_dump($n);

