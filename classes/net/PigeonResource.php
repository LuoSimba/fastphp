<?php
namespace net;

interface PigeonResource 
{
    function id(): int;

    function onData(): void;

    function closed(): bool;

    function getRecvCount(): int;

    function getCreateTime(): int;
    function getUpdateTime(): int;
}

