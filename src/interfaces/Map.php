<?php
namespace Kethner\cdcBridge\interfaces;

interface Map
{
    /**
     * Maps response to dataObject
     */
    public static function mapResponse($response);

    /**
     * Maps dataObject to request
     */
    public static function mapRequest($dataObject);
}
