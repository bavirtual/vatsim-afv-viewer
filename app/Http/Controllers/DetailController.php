<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function data()
    {
        $unicom = $this->unicom();
        $unicomCount = 0;
        $morethan1 = false;
        foreach($unicom as $uni) {
            if($uni > 1) {
                $morethan1 = true;
            }
            $unicomCount = $unicomCount + $uni;
        }

        return view('details', compact('unicom','unicomCount','morethan1'));
    }

    public function unicom()
    {
        $afvData = AfvApiController::getClients();
        $unicomTransceivers = [];

        foreach($afvData as $callsign => $afvDatum) {
            foreach($afvDatum['transceivers'] as $transceiver) {
                if($transceiver->frequency == 122800000) {
                    if(isset($unicomTransceivers[$callsign])) {
                        $unicomTransceivers[$callsign] = $unicomTransceivers[$callsign] + 1;
                    } else {
                        $unicomTransceivers[$callsign] = 1;
                    }
                }
            }
        }

        return $unicomTransceivers;
    }

    public function freq(Request $request)
    {

    }
}
