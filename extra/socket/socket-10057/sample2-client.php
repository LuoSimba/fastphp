<?php

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

$bool = socket_connect($so, 'localhost', 9999);

var_dump($bool);

