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
                        <h5 style="color: white">Unicom Transceiver Count: {{ $unicomCount }}</h5>
                        @if($morethan1)
                            <h5 style="color: white">The following client(s) have more than 1 UNICOM transceiver:</h5>
                            <ul style="color: white;">
                                @foreach($unicom as $callsign => $count)
                                    @if($count > 1)
                                        <li style="color: white;">{{ $callsign }} - {{ $count }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>

@endsection
