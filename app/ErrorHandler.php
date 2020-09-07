<?php


namespace App;



class ErrorHandler
{
    public function __construct($message, $redirectUrl)
    {
        echo "redirecionando";
        header("Location: $redirectUrl");
    }
}