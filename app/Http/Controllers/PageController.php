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

        if (isset($data->controllers[$callsign])){ $data = $data->controllers->$callsign->transceivers; }
        elseif (isset($data->pilots[$callsign])) { $data = $data->pilots->$callsign->transceivers; }
        elseif (isset($data->other[$callsign])) { $data = $data->other->$callsign->transceivers; }
        else { $data = []; }

        return view('full_map', compact('data'));
    }

    public function index()
    {
        return view('index');
    }
}
