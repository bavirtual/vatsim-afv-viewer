<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class PageController extends Controller
{
    public function fullMap()
    {
        $data = json_decode(FsdDataController::getClients());
        return view('full_map');
    }

    public function index()
    {
        return view('index');
    }
}
