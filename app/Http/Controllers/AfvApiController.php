<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\TransferException;

class AfvApiController extends Controller
{
    //////////////////////////////////////////////////////
    // This class/controller handles all requests to    //
    // the AFV Voice server.                            //
    //////////////////////////////////////////////////////
    private static $domain = 'https://voice1.vatsim.uk/';
    private static $apiVersion = 1;
    private static $timeout = 5; // In seconds

    protected static $client; // GuzzleHttp\Client instance

    protected static function init() // __construct() doesn't work with static classes
    {
        $apiUri = sprintf('%sapi/v%d/', self::$domain, self::$apiVersion);
        
        self::$client = new Client([
            'base_uri' => $apiUri,
            'timeout' => self::$timeout,
        ]);
    }

    public static function getClients()
    {
        self::init();

        try {
            $response = self::$client->request('GET', 'network/online/callsigns');
        } catch (TransferException $e) {
            return Cache::get('afv_clients_latest', []); // If API fails, return the latest data (or empty array if it doesn't exist)
        }

        $json = (string) $response->getBody();
        $clients = self::reformat($json);
 
        Cache::put('afv_clients_latest', $clients); // Cache in case the AFV server fails to respond

        return $clients;
    }

    /**
     * Formats from [{...},{...},...] to {'...':{...}, '...':{...},...}
     */
    protected static function reformat($json)
    {
        $clients = json_decode($json);
        $newFormat = [];

        foreach($clients as $key => $client)
        {
            $newFormat[$client->callsign] = [];
            $newFormat[$client->callsign]['transceivers'] = $client->transceivers;
            unset($clients[$key]); // Free memory
        }

        return $newFormat;
    }
}
