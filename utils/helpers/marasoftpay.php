<?php

if (! function_exists("marasoftpay"))
{
    function marasoftpay() {
        
        return app()->make('laravel-marasoftpay');
    }
}