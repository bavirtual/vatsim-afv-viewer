<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function data()
    {
        $afvData = AfvApiController::getClients();
        $unicomTransceivers = [];
        $unicomWestNATransceivers = [];
        $unicomEastNASATransceivers = [];
        $unicomWestEUTransceivers = [];
        $unicomEastEUTransceivers = [];
        $unicomOtherTransceivers = [];

        $unicomTransceiverCount = 0;
        $unicomWestNATransceiverCount = 0;
        $unicomEastNASATransceiverCount = 0;
        $unicomWestEUTransceiverCount = 0;
        $unicomEastEUTransceiverCount = 0;
        $unicomOtherTransceiverCount = 0;

        foreach($afvData as $callsign => $afvDatum) {
            foreach($afvDatum['transceivers'] as $transceiver) {
                if($transceiver->frequency == 122800000) {
                    if(isset($unicomTransceivers[$callsign])) {
                        $unicomTransceivers[$callsign] = $unicomTransceivers[$callsign] + 1;
                    } else {
                        $unicomTransceivers[$callsign] = 1;
                    }
                }
                if($transceiver->frequency == 122800001) {
                    if(isset($unicomWestNATransceivers[$callsign])) {
                        $unicomWestNATransceivers[$callsign] = $unicomWestNATransceivers[$callsign] + 1;
                    } else {
                        $unicomWestNATransceivers[$callsign] = 1;
                    }
                }
                if($transceiver->frequency == 122800002) {
                    if(isset($unicomEastNASATransceivers[$callsign])) {
                        $unicomEastNASATransceivers[$callsign] = $unicomEastNASATransceivers[$callsign] + 1;
                    } else {
                        $unicomEastNASATransceivers[$callsign] = 1;
                    }
                }
                if($transceiver->frequency == 122800003) {
                    if(isset($unicomWestEUTransceivers[$callsign])) {
                        $unicomWestEUTransceivers[$callsign] = $unicomWestEUTransceivers[$callsign] + 1;
                    } else {
                        $unicomWestEUTransceivers[$callsign] = 1;
                    }
                }
                if($transceiver->frequency == 122800004) {
                    if(isset($unicomEastEUTransceivers[$callsign])) {
                        $unicomEastEUTransceivers[$callsign] = $unicomEastEUTransceivers[$callsign] + 1;
                    } else {
                        $unicomEastEUTransceivers[$callsign] = 1;
                    }
                }
                if($transceiver->frequency == 122800005) {
                    if(isset($unicomOtherTransceivers[$callsign])) {
                        $unicomOtherTransceivers[$callsign] = $unicomOtherTransceivers[$callsign] + 1;
                    } else {
                        $unicomOtherTransceivers[$callsign] = 1;
                    }
                }
            }
        }

        foreach($unicomTransceivers as $unicomTransceiver) {
            $unicomTransceiverCount = $unicomTransceiverCount + $unicomTransceiver;
        }

        foreach($unicomWestNATransceivers as $westNATransceiver) {
            $unicomWestNATransceiverCount = $unicomWestNATransceiverCount + $westNATransceiver;
        }

        foreach($unicomEastNASATransceivers as $eastNASATransceiver) {
            $unicomEastNASATransceiverCount = $unicomEastNASATransceiverCount + $eastNASATransceiver;
        }

        foreach($unicomWestEUTransceivers as $WestEUTransceiver) {
            $unicomWestEUTransceiverCount = $unicomWestEUTransceiverCount + $EUTransceiver;
        }

        foreach($unicomEastEUTransceivers as $EUTransceiver) {
            $unicomEastEUTransceiverCount = $unicomEastEUTransceiverCount + $EUTransceiver;
        }

        foreach($unicomOtherTransceivers as $otherTransceiver) {
            $unicomOtherTransceiverCount = $unicomOtherTransceiverCount + $otherTransceiver;
        }

        return view('details', compact('unicomWestNATransceivers','unicomEastNASATransceivers', 'unicomWestEUTransceivers', 'unicomEastEUTransceivers', 'unicomOtherTransceivers', 'unicomWestNATransceiverCount', 'unicomEastNASATransceiverCount', 'unicomEastEUTransceiverCount', 'unicomWestEUTransceiverCount', 'unicomOtherTransceiverCount', 'unicomTransceivers', 'unicomTransceiverCount'));
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
