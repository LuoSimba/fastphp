<?php
namespace net;

/**
 * 管理 socket 连接的容器
 */
interface PigeonContainer
{

    function get(int $id): PigeonResource;

    function del(int $id): void;

    function add(PigeonResource $conn): void;
}

