<?php

namespace hash {

    function sha256(string $text)
    {
        return hash('sha256', $text, true);
    }
}

