<?php
//
// 同样的地址绑定两遍，报错
//

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($so, '127.0.0.1', 9999);

// error 10022
socket_bind($so, '127.0.0.1', 9999);

