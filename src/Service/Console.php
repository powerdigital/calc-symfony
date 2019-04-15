<?php

namespace App\Service;

class Console
{
    public function out(string $message)
    {
        print $message . PHP_EOL;
    }

    public function error(string $message)
    {
        printf ("\033[31m%s\033[0m\n", $message);
    }

    public function success(string $message)
    {
        printf ("\e[32m%s\e[0m\n", $message);
    }
}
