@extends('_layouts.master')

@section('pageTitle')
    ATIS Map - <span id="online-count"><i class="la la-refresh la-spin"></i></span>
@endsection


@section('page_css')
    <link rel="stylesheet" type="text/css" href="{{ app_asset_path('vendors/leaflet/leaflet.css') }}">
@endsection


@section('page_js')
    <script type="text/javascript" src="{{ app_asset_path('vendors/leaflet/leaflet.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/leaflet/leaflet-geodesic.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/leaflet/leaflet-rotation.js') }}"></script>
@endsection


@section('content')

    <div class="row">
        <div class="col-md-2">
            <section class="card">
                <div class="card-content">
                    <div class="card-body" id="atisList" style="height: calc(100vh - 300px); overflow: auto;">

                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-10">
            <section class="card">
                <div class="card-content">
                    <div class="card-body" id="flightMap" style="height: calc(100vh - 300px);"></div>
                </div>
            </section>
        </div>
    </div>

    <script>
        var map = L.map('flightMap').setView([44.341393, -3.915340], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        var pin = L.icon({
            iconUrl: '{{ asset_path('img/map/pin.png') }}',
            iconSize: [10, 17],
            iconAnchor: [5, 17]
        });

        mapMarkers = [];
        onlineATIS = [];
        $.ajax({
            type: 'get',
            url: '{{ route('atis-map-data') }}',
            success: function(data) {
                data.forEach(function (atis) {
                    var callsign = atis.callsign;
                    var frequency = atis.transceivers[0].frequency;
                    var lat = atis.transceivers[0].latDeg;
                    var lon = atis.transceivers[0].lonDeg;
                    var msl = atis.transceivers[0].altMslM;

                    var content = '<b>' + callsign +'</b><br>';
                    content += frequency;

                    var marker = L.marker([lat, lon], {
                        icon: L.icon({
                            iconUrl: '{{ asset_path('img/map/pin.png') }}',
                            iconSize: [10, 17],
                            iconAnchor: [5, 17]
                        }),
                    });

                    if(!callsign.includes('ATIS')) {
                        var RadiusMeters = (1.25 * Math.sqrt(msl * 3.28084)) * 1852;
                        L.circle([lat, lon], {radius: RadiusMeters, fillOpacity: 0}).addTo(map);
                    }

                    marker.addTo(map).bindPopup(content);
                    mapMarkers.push(marker);
                    onlineATIS.push({callsign: callsign, freq: frequency});
                });
                if(mapMarkers.length > 0) {
                    map.fitBounds(L.featureGroup(mapMarkers).getBounds());
                    onlineATIS.sort(function(a, b) {
                        return ((a.callsign < b.callsign) ? -1 : ((a.callsign > b.callsign) ? 1 : 0));
                    });
                    onlineATIS.forEach(function (atis) {
                        $('#atisList').append('<h5 style="margin-bottom: 0;">' + atis.callsign + ' - ' + atis.freq + '</h5>');
                    });
                    $('#online-count').html(onlineATIS.length + ' Voice Clients Connected');
                } else {
                    $('#atisList').append('<h5 style="margin-bottom: 0;">No Voice Clients</h5>');
                    $('#online-count').html('0 Voice Clients Connected');
                }
            }
        });

        setInterval(function() {
            $.ajax({
                type: 'get',
                url: '{{ route('atis-map-data') }}',
                success: function (data) {
                    for (var i = 0; i < mapMarkers.length; i++) {
                        map.removeLayer(mapMarkers[i]);
                    }
                    mapMarkers = mapMarkers.filter(function(item) {
                        return item === 'test';
                    });
                    onlineATIS = onlineATIS.filter(function(item) {
                        return item === 'test';
                    });
                    $('#atisList').html('');
                    data.forEach(function (atis) {
                        var callsign = atis.callsign;
                        var frequency = atis.transceivers[0].frequency;
                        var lat = atis.transceivers[0].latDeg;
                        var lon = atis.transceivers[0].lonDeg;
                        var msl = atis.transceivers[0].altMslM;

                        var content = '<b>' + callsign + '</b><br>';
                        content += frequency;

                        var marker = L.marker([lat, lon], {
                            icon: L.icon({
                                iconUrl: '{{ asset_path('img/map/pin.png') }}',
                                iconSize: [10, 17],
                                iconAnchor: [5, 17]
                            }),
                        });

                        if(!callsign.includes('ATIS')) {
                            var RadiusMeters = (1.25 * Math.sqrt(msl * 3.28084)) * 1852;
                            L.circle([lat, lon], {radius: RadiusMeters, fillOpacity: 0}).addTo(map);
                        }

                        marker.addTo(map).bindPopup(content);
                        mapMarkers.push(marker);
                        onlineATIS.push({callsign: callsign, freq: frequency});
                    });

                    if(mapMarkers.length > 0) {
                        onlineATIS.sort(function(a, b) {
                            return ((a.callsign < b.callsign) ? -1 : ((a.callsign > b.callsign) ? 1 : 0));
                        });
                        onlineATIS.forEach(function (atis) {
                            $('#atisList').append('<h5 style="margin-bottom: 0;">' + atis.callsign + ' - ' + atis.freq + '</h5>');
                        });
                        $('#online-count').html(onlineATIS.length + ' Voice Clients Connected');
                    } else {
                        $('#atisList').append('<h5 style="margin-bottom: 0;">No Voice Clients</h5>');
                        $('#online-count').html('0 Voice Clients Connected');
                    }
                }
            });
        }, 60000);

    </script>

@endsection