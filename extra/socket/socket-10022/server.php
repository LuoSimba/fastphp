<?php
// 
// create() -> bind() -> listen()
//

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

// no bind() ...

socket_listen($so, 5);

