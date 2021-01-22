<?php

namespace App;

class ErrorHandler
{
    public function __construct($message, $redirectUrl)
    {
        header("Location: $redirectUrl");
    }
}