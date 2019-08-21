<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientError;
use GuzzleHttp\Exception\ServerError;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\TransferException;

class FsdDataController extends Controller
{
    //////////////////////////////////////////////////////
    // This class/controller handles all requests to    //
    // the AFV Voice server.                            //
    //////////////////////////////////////////////////////
    private static $domain = 'https://afv-beta.vatsim.net/';
    private static $timeout = 5; // In seconds

    protected static $client; // GuzzleHttp\Client instance


    protected static function init()
    {        
        self::$client = new Client([
            'base_uri' => self::$domain,
            'timeout' => self::$timeout,
            //'verify' => false,
        ]);
    }


    public static function getClients()
    {
        self::init();

        try {
            $response = self::$client->request('GET', 'vatsim-data');
        } catch (TransferException | ClientError  | ServerError $e) {
            return response()->json(Cache::get('fsd_clients_latest', ['pilots' => [], 'controllers' => [], 'other' => []])); // If API fails, return the latest data (or empty array if it doesn't exist)
        }

        $json = (string) $response->getBody();
        $clients = self::addAfvData($json);
        Cache::put('fsd_clients_latest', $clients); // Cache in case the AFV server fails to respond

        return response()->json($clients);
    }


    protected static function addAfvData($json)
    {
        $clients = json_decode($json);
        $afvData = AfvApiController::getClients();
        
        $output = [];
        $output['pilots'] = [];
        $output['controllers'] = [];
        $output['other'] = []; // ATIS Bots, etc... (Any voice connection not present in FSD)

        foreach($clients->pilots as $key => $pilot)
        {
            $callsign = $pilot->callsign;
            unset($pilot->callsign);
            $output['pilots'][$callsign] = $pilot;
            unset($clients->pilots->$key); // Free memory

            if (isset($afvData[$callsign])){
                $output['pilots'][$callsign]->transceivers = $afvData[$callsign]['transceivers'];
                unset($afvData[$callsign]);
            } else{
                $output['pilots'][$callsign]->transceivers = [];
            }
        }
        foreach($clients->controllers as $key => $controller)
        {
            $callsign = $controller->callsign;
            unset($controller->callsign);
            $output['controllers'][$callsign] = $controller;
            unset($clients->controllers->$key); // Free memory

            if (isset($afvData[$callsign])){
                $output['controllers'][$callsign]->transceivers = $afvData[$callsign]['transceivers'];
                unset($afvData[$callsign]);
            } else{
                $output['controllers'][$callsign]->transceivers = [];
            }
        }
        foreach($afvData as $callsign => $data)
        {
            $output['other'][$callsign] = $data;
        }

        return $output;
    }
}