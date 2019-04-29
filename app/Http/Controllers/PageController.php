<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class PageController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function atisMap()
    {
        return view('atis-map');
    }

    public function atisMapData()
    {
        $client = new Client(['verify' => false ]);
        $data = $client->get('https://voice1.vatsim.uk/api/v1/network/online/callsigns')->getBody()->__toString();
        return response()->json(json_decode($data));
    }
}
