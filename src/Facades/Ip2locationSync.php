<?php
namespace DragAndPublish\Ip2locationSync\Facades;

use Illuminate\Support\Facades\Facade;

class Ip2locationSync extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ip2location-sync';
    }
}
