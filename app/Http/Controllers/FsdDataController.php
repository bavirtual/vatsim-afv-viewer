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
    // private static $domain = 'https://afv-beta.vatsim.net/';
    private static $domain = 'http://cluster.data.vatsim.net/';
    private static $timeout = 5; // In seconds

    protected static $client; // GuzzleHttp\Client instance


    public function __construct($local = false)
    {
        if (!$local) {
            $this->middleware('csrf.get');
        }
        self::$client = new Client([
            'base_uri' => self::$domain,
            'timeout' => self::$timeout,
            //'verify' => false,
        ]);
    }


    public function getClients($array = false)
    {
        $clients = Cache::remember('fsd_clients', 10, function () {
            try {
                // $response = self::$client->request('GET', 'vatsim-data');
                $response = self::$client->request('GET', 'vatsim-data.json');
            } catch (TransferException | ClientError  | ServerError $e) {
                return Cache::get('fsd_clients_latest', ['pilots' => [], 'controllers' => [], 'other' => []]); // If API fails, return the latest data (or empty array if it doesn't exist)
            }

            $json = (string)$response->getBody();
            $clients = json_encode($this->addAfvData($json));
            Cache::put('fsd_clients_latest', $clients); // Cache in case the AFV server fails to respond
            return $clients;
        });

        if ($array == true) {
            return json_decode($clients);
        } else {
            return response()->json(json_decode($clients));
        }
    }


    protected function addAfvData($json)
    {
        $status_data = json_decode($json);
        $clients = $status_data->clients;
        $afvData = AfvApiController::getClients();

        $output = [];
        $output['pilots'] = [];
        $output['controllers'] = [];
        $output['other'] = []; // ATIS Bots, etc... (Any voice connection not present in FSD)

        foreach ($clients as $key => $client) {
            if ($client->clienttype == "PILOT") {
                $callsign = $client->callsign;
                unset($client->callsign);
                $output['pilots'][$callsign] = $client;
                unset($clients->$key); // Free memory

                if (isset($afvData[$callsign])) {
                    $output['pilots'][$callsign]->transceivers = $afvData[$callsign]['transceivers'];
                    unset($afvData[$callsign]);
                } else {
                    $output['pilots'][$callsign]->transceivers = [];
                }
            } else {
                $callsign = $client->callsign;
                unset($client->callsign);

                $output['controllers'][$callsign] = $client;
                unset($clients->$key); // Free memory

                if (isset($afvData[$callsign])) {
                    $output['controllers'][$callsign]->transceivers = $afvData[$callsign]['transceivers'];
                    unset($afvData[$callsign]);
                } else {
                    $output['controllers'][$callsign]->transceivers = [];
                }
            }
        }
        foreach ($afvData as $callsign => $data) {
            $output['other'][$callsign] = $data;
        }

        return $output;
    }
}
