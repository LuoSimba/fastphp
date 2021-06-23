<?php
// 
// 不同的进程也不能抢同一个地址
//
// 将本程序执行两遍，第二遍会报错 10048
//

$so = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

// 地址的绑定是全局的，所有的进程都能知道
//
// > netstat -ano -p TCP -q
//
// 协议  本地地址        外部地址   状态   PID
// TCP   127.0.0.1:9999  0.0.0.0:0  BOUND  14780
socket_bind($so, 'localhost', 9999);

sleep(600);

