<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function fullMap(Request $request)
    {
        $callsign = $request->input('callsign', '');
        $data = (new FsdDataController())->getClients(true, true);

        if (array_key_exists($callsign, $data->controllers)){ $data = $data->controllers->$callsign->transceivers; }
        elseif (array_key_exists($callsign, $data->pilots)) { $data = $data->pilots->$callsign->transceivers; }
        elseif (array_key_exists($callsign, $data->other)) { $data = $data->other->$callsign->transceivers; }
        else { $data = []; }

        return view('full_map', compact('data'));
    }

    public function index()
    {
        return view('index');
    }
}
