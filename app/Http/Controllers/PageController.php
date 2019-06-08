<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class PageController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function atisMapData()
    {
        $client = new Client(['verify' => false ]);
        $voice_clients = json_decode($client->get('https://voice1.vatsim.uk/api/v1/network/online/callsigns')->getBody()->__toString());
        $network_clients = json_decode($client->get('https://afv-beta.vatsim.net/vatsim-data')->getBody()->__toString());

        foreach($voice_clients as $voice_client) {
            if(strpos($voice_client->callsign, '_DEL') !== false || strpos($voice_client->callsign, '_GND') !== false || strpos($voice_client->callsign, '_TWR') !== false || strpos($voice_client->callsign, '_APP') !== false ||strpos($voice_client->callsign, '_DEP') !== false || strpos($voice_client->callsign, '_CTR') !== false || strpos($voice_client->callsign, '_FSS') !== false) {
                // controller
                $controller_data = $this->getControllerData($voice_client->callsign, $network_clients);
                $voice_client->type = 'controller';
                if($controller_data == null) {
                    $voice_client->member_name = 'NOT FOUND';
                } else {
                    $voice_client->member_name = $controller_data->member->name;
                }

            } elseif (!strpos($voice_client->callsign, '_') !== false) {
                // pilot
                $pilot_data = $this->getPilotData($voice_client->callsign, $network_clients);
                $voice_client->type = 'pilot';
                if($pilot_data == null) {
                    $voice_client->member_name = 'NOT FOUND';
                    $voice_client->altitude = 'N/A';
                    $voice_client->heading = 'N/A';
                    $voice_client->ground_speed = 'N/A';
                    $voice_client->route = 'N/A';
                } else {
                    $voice_client->member_name = $pilot_data->member->name;
                    $voice_client->altitude = $pilot_data->altitude;
                    $voice_client->heading = $pilot_data->heading;
                    $voice_client->ground_speed = $pilot_data->speed;
                    if(isset($pilot_data->plan)) {
                        $voice_client->route = $pilot_data->plan->departure.'/'.$pilot_data->plan->arrival;
                    } else {
                        $voice_client->route = 'N/A';
                    }
                }
            } else {
                // atis
                $controller_data = $this->getControllerData($voice_client->callsign, $network_clients);
                $voice_client->type = 'atis';
                if($controller_data == null) {
                    $voice_client->member_name = 'NOT FOUND';
                } else {
                    $voice_client->member_name = $controller_data->member->name;
                }
            }
        }

        return response()->json($voice_clients);
    }

    protected function getPilotData($callsign, $data)
    {
        foreach ($data->pilots as $pilot) {
            if($pilot->callsign == $callsign) {
                return $pilot;
            }
        }
        return null;
    }

    protected function getControllerData($callsign, $data)
    {
        foreach ($data->controllers as $controller) {
            if($controller->callsign == $callsign) {
                return $controller;
            }
        }
        return null;
    }
}
