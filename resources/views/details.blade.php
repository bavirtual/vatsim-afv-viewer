@extends('_layouts.master')

@section('pageTitle')
    Transceiver Details
@endsection


@section('content')

    <div class="row">
        <div class="col-12">
            <section class="card" style="height: calc(100% - 1.875rem);">
                <div class="card-content h-100 bg-dark rounded">
                    <div class="card-body h-100 bg-dark rounded">
                        <h5 style="color: white">TOTAL Unicom Transceiver Count: {{ $unicomTransceiverCount + $unicomWestNATransceiverCount + $unicomEastNASATransceiverCount + $unicomEastEUTransceiverCount + $unicomWestEUTransceiverCount + $unicomOtherTransceiverCount}}</h5>
                        <hr>
                        <h5 style="color: white">Regular Unicom Transceiver Count: {{ $unicomTransceiverCount }}</h5>
                        <h5 style="color: white">The following client(s) have more than 1 Regular UNICOM Transceiver:</h5>
                        <ul style="color: white;">
                            @foreach($unicomTransceivers as $callsign => $count)
                                @if($count > 1) <li style="color: white;">{{ $callsign }} - {{ $count }}</li> @endif
                            @endforeach
                        </ul>
                        <hr>
                        <h5 style="color: white">Unicom West NA Transceiver Count: {{ $unicomWestNATransceiverCount }}</h5>
                        <h5 style="color: white">The following client(s) have more than 1 Unicom West NA Transceiver:</h5>
                        <ul style="color: white;">
                            @foreach($unicomWestNATransceivers as $callsign => $count)
                                @if($count > 1) <li style="color: white;">{{ $callsign }} - {{ $count }}</li> @endif
                            @endforeach
                        </ul>
                        <hr>
                        <h5 style="color: white">Unicom East NA and SA Transceiver Count: {{ $unicomEastNASATransceiverCount }}</h5>
                        <h5 style="color: white">The following client(s) have more than 1 Unicom East NA and SA Transceiver:</h5>
                        <ul style="color: white;">
                            @foreach($unicomEastNASATransceivers as $callsign => $count)
                                @if($count > 1) <li style="color: white;">{{ $callsign }} - {{ $count }}</li> @endif
                            @endforeach
                        </ul>
                        <hr>
                        <h5 style="color: white">Unicom West EU Transceiver Count: {{ $unicomWestEUTransceiverCount }}</h5>
                        <h5 style="color: white">The following client(s) have more than 1 West Unicom EU Transceiver:</h5>
                        <ul style="color: white;">
                            @foreach($unicomWestEUTransceivers as $callsign => $count)
                                @if($count > 1) <li style="color: white;">{{ $callsign }} - {{ $count }}</li> @endif
                            @endforeach
                        </ul>
                        <hr>
                        <h5 style="color: white">Unicom East EU Transceiver Count: {{ $unicomEastEUTransceiverCount }}</h5>
                        <h5 style="color: white">The following client(s) have more than 1 East Unicom EU Transceiver:</h5>
                        <ul style="color: white;">
                            @foreach($unicomEastEUTransceivers as $callsign => $count)
                                @if($count > 1) <li style="color: white;">{{ $callsign }} - {{ $count }}</li> @endif
                            @endforeach
                        </ul>
                        <hr>
                        <h5 style="color: white">Unicom Other Transceiver Count: {{ $unicomOtherTransceiverCount }}</h5>
                        <h5 style="color: white">The following client(s) have more than 1 UNICOM Other Transceiver:</h5>
                        <ul style="color: white;">
                            @foreach($unicomOtherTransceivers as $callsign => $count)
                                @if($count > 1) <li style="color: white;">{{ $callsign }} - {{ $count }}</li> @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>

@endsection
