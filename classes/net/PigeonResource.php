<?php
namespace net;

interface PigeonResource 
{
    function id(): int;

    function onData(): void;

    function closed(): bool;
}

