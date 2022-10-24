<?php

namespace SwapnealDev\GoogleContact;

use Illuminate\Support\Facades\Facade;

class GoogleContactFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-google-contact';
    }
}
