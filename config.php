<?php
// config.php

function app_env(string $name, ?string $default = null): ?string
{
    $value = getenv($name);

    return ($value !== false && $value !== '')
        ? $value
        : $default;
}
