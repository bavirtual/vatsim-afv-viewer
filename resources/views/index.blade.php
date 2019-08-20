@extends('_layouts.master')

@section('pageTitle')
    <span id="online-count"><i class="la la-refresh la-spin"></i></span>
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
        <div class="col-12 col-md-3">
            <section class="card" style="height: calc(100% - 1.875rem);">
                <div class="card-content h-100">
                    <div class="card-body h-100">
                        <button class="btn btn-sm" id="togglePilotRings">Toggle Pilot Rings</button>
                        <button class="btn btn-sm" id="toggleAtcRings">Toggle ATC Rings</button>
                        <br><br>
                        <div id="atis-list" style="height: calc(100vh - 300px); overflow: auto;">

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-12 col-md-9 mb-5 mb-md-0">
            <section class="card" style="height: calc(100% - 1.875rem);">
                <div class="card-content h-100">
                    <div class="card-body h-100 rounded" id="flightMap" style="min-height: calc(100vh - 300px);"></div>
                </div>
            </section>
        </div>
    </div>

    <script>
        firstLoad = true;
        pilotRings = true;
        atcRings = true;

        var map = L.map('flightMap').setView([44.341393, -3.915340], 2);
        
        // Map Layers
        var basic = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map),
        streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }),
        satellite = L.tileLayer(
            'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
             attribution: '&copy; ' + '<a href="http://www.esri.com/">Esri</a>'
        });
        var maps = {"Basic": basic, "Streets": streets, "Satellite": satellite};
        L.control.layers(maps).addTo(map);

        ranges = [];
        markers = [];
        clients = {};

        $('#togglePilotRings').click(function () {
            if(pilotRings == true) {
                $('path[stroke="#ce6262"]').hide();
                pilotRings = false;
            } else {
                $('path[stroke="#ce6262"]').show();
                pilotRings = true;
            }
        });

        $('#toggleAtcRings').click(function () {
            if(atcRings == true) {
                $('path[stroke="#418041"]').hide();
                atcRings = false;
            } else {
                $('path[stroke="#418041"]').show();
                atcRings = true;
            }
        });

        reloadMapData();
        setInterval(function() {
            reloadMapData();
        }, 15000);
        
        
        function reloadMapData() {
            $.ajax({
                type: 'get',
                url: '{{ route('test') }}',
                success: function(data) {
                    for (var i = 0; i < markers.length; i++) {
                        map.removeLayer(markers[i]);
                    }
                    markers = [];
                    for (var i = 0; i < ranges.length; i++) {
                        map.removeLayer(ranges[i]);
                    }
                    ranges = [];
                    $('path.leaflet-interactive').remove();
                    $('#atis-list').html(''); // Cleans the list


                    for(callsign in data.pilots){
                        var content = '<b>' + callsign + '</b><br>';
                            content += data.pilots[callsign].member.name +'<br>';
                            if (data.pilots[callsign].plan.departure && data.pilots[callsign].plan.arrival){
                                content += data.pilots[callsign].plan.departure +' -> ' + data.pilots[callsign].plan.arrival + '<br>';
                            } else {
                                content += 'No Flightplan Sent<br>';
                            }
                            content += data.pilots[callsign].altitude + 'ft - GS' + data.pilots[callsign].speed + '<br>';
                        
                        lat = data.pilots[callsign].latitude;
                        lon = data.pilots[callsign].longitude;

                        markers.push(L.marker([lat, lon], {
                            icon: L.icon({
                                iconUrl: '{{ asset_path('img/map/plane.png') }}',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15],
                                popupAnchor: [0, -15]
                            }),
                            rotationAngle: data.pilots[callsign].heading,
                            rotationOrigin: 'center'
                        }).bindPopup(content).addTo(map));

                        var transceivers = data.pilots[callsign].transceivers;
                        transceivers.forEach(function(transceiver){
                            //var RadiusMeters = (1.25 * Math.sqrt(transceiver.altMslM * 3.28084)) * 1852;
                            var RadiusMeters = 4193.18014745372 * Math.sqrt(transceiver.altMslM);
                            ranges.push(L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#ce6262', weight: 1}).bindPopup(content).addTo(map));
                        });

                        clients[callsign] = data.pilots[callsign];
                    }

                    for(callsign in data.controllers){
                        var content = '<b>' + callsign + '</b><br>';
                            content += data.controllers[callsign].member.name + '<br>';
                            content += data.controllers[callsign].frequency + '<br>';
                        
                        lat = data.controllers[callsign].latitude;
                        lon = data.controllers[callsign].longitude;

                        if(! callsign.includes('_ATIS')){
                            markers.push(L.marker([lat, lon], {
                                icon: L.icon({
                                    iconUrl: '{{ asset_path('img/map/green-pin.png') }}',
                                    iconSize: [10, 16],
                                    iconAnchor: [5, 16],
                                    popupAnchor: [0, -16]
                                })
                            }).bindPopup(content).addTo(map));

                            var transceivers = data.controllers[callsign].transceivers;
                            transceivers.forEach(function(transceiver){
                                var content = '<b>' + callsign + '</b><br>';
                                    content += data.controllers[callsign].member.name + '<br>';
                                    content += (transceiver.frequency/1000000).toFixed(3) + '<br>';
                                //var RadiusMeters = (1.25 * Math.sqrt(transceiver.altMslM * 3.28084)) * 1852;
                                var RadiusMeters = 4193.18014745372 * Math.sqrt(transceiver.altMslM);
                                ranges.push(L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#418041', weight: 1}).bindPopup(content).addTo(map));

                                /*markers.push(L.marker([transceiver.latDeg, transceiver.lonDeg], {
                                    icon: L.icon({
                                        iconUrl: '{{ asset_path('img/map/green-pin.png') }}',
                                        iconSize: [10, 16],
                                        iconAnchor: [5, 16],
                                        popupAnchor: [0, -16]
                                    })
                                }).bindPopup(content).addTo(map));*/
                            });
                        }

                        clients[callsign] = data.controllers[callsign];
                    }

                    for(callsign in data.other){
                        clients[callsign] = data.other[callsign];
                    }

                    /*data.forEach(function (client) {
                        client['transceivers'].forEach(function (transceiver) {
                            var callsign = client.callsign;
                            var name = client.member_name;
                            var frequency = transceiver.frequency;
                            frequency = (frequency/1000000).toFixed(3);
                            var lat = transceiver.latDeg;
                            var lon = transceiver.lonDeg;
                            var msl = transceiver.altMslM;

                            var content = '<b>' + callsign +'</b><br>';
                            if(client.type != 'atis' || name != null){ // Doesn't show 'Not Found' for ATIS Bots
                                content += '<b>' + name +'</b><br>';
                            }
                            if(client.type == 'pilot') {
                                content += client.altitude + 'ft<br>';
                                content += client.route + '<br>';
                            }
                            content += frequency;

                            if(client.type == 'atis') {
                                var marker = L.marker([lat, lon], {
                                    icon: L.icon({
                                        iconUrl: '{{ asset_path('img/map/pin.png') }}',
                                        iconSize: [10, 17],
                                        iconAnchor: [5, 17],
                                        popupAnchor: [0, -17]
                                    }),
                                });
                            // } else if (callsign.includes('_DEL') || callsign.includes('_GND') || callsign.includes('_TWR') || callsign.includes('_APP') || callsign.includes('_DEP') || callsign.includes('_CTR') || callsign.includes('_FSS')) { // controller
                            } else if(client.type == 'controller') { // controller
                                var marker = L.marker([lat, lon], {
                                    icon: L.icon({
                                        iconUrl: '{{ asset_path('img/map/green-pin.png') }}',
                                        iconSize: [10, 16],
                                        iconAnchor: [5, 16],
                                        popupAnchor: [0, -16]
                                    }),
                                });
                            } else { // plane
                                if(client.heading == 'N/A') {
                                    var marker = L.marker([lat, lon], {
                                        icon: L.icon({
                                            iconUrl: '{{ asset_path('img/map/plane.png') }}',
                                            iconSize: [30, 30],
                                            iconAnchor: [15, 15],
                                            popupAnchor: [0, -15]
                                        }),
                                    });
                                } else {
                                    var marker = L.marker([lat, lon], {
                                        icon: L.icon({
                                            iconUrl: '{{ asset_path('img/map/plane.png') }}',
                                            iconSize: [30, 30],
                                            iconAnchor: [15, 15],
                                            popupAnchor: [0, -15]
                                        }),
                                        rotationAngle: client.heading,
                                        rotationOrigin: 'center'
                                    });
                                }
                            }

                            if (callsign.includes('_DEL') || callsign.includes('_GND') || callsign.includes('_TWR') || callsign.includes('_APP') || callsign.includes('_DEP') || callsign.includes('_CTR') || callsign.includes('_FSS')) {
                                var RadiusMeters = (1.25 * Math.sqrt(msl * 3.28084)) * 1852;
                                L.circle([lat, lon], {radius: RadiusMeters, fillOpacity: 0, color: '#418041'}).addTo(map);
                            } else if (!callsign.includes('_')) {
                                var RadiusMeters = (1.25 * Math.sqrt(msl * 3.28084)) * 1852;
                                L.circle([lat, lon], {radius: RadiusMeters, fillOpacity: 0, color: '#ce6262'}).addTo(map);
                            }

                            marker.addTo(map).bindPopup(content);
                            mapMarkers.push(marker);
                            onlineTransceivers.push({callsign: callsign, freq: frequency});
                        });
                        
                        //

                    });*/
                    
                    if(pilotRings == 0) {
                        $('path[stroke="#ce6262"]').hide();
                    }
                    if(atcRings == 0) {
                        $('path[stroke="#418041"]').hide();
                    }

                    if(markers.length > 0) {
                        if(firstLoad == true) {
                            map.fitBounds(L.featureGroup(markers).getBounds());
                            firstLoad = false;
                        }

                        clients = sortOnKeys(clients);

                        // [{client: '', frequencies:['123.000','122.800']]]
                        for(callsign in clients){
                            // add the frequency to array
                            var frequencies = [];
                            clients[callsign].transceivers.forEach(function(transceiver){
                                frequencies.push((transceiver.frequency/1000000).toFixed(3));
                            });
                            if (frequencies.length > 0){
                                $('#atis-list').append('<h5 style="margin-bottom: 0;">' + callsign + ' - ' + frequencies.join(',') + '</h5>');
                            } else {
                                $('#atis-list').append('<h5 style="margin-bottom: 0;">' + callsign + ' - ' + clients[callsign].frequency + ' [TXT Only]</h5>');
                            }
                        }

                        $('#online-count').html(Object.keys(clients).length + ' Voice Clients Connected');
                    } else {
                        $('#atis-list').append('<h5 style="margin-bottom: 0;">No Voice Clients</h5>');
                        $('#online-count').html('0 Voice Clients Connected');
                    }
                }
            });
        }

        function sortOnKeys(dict) {
            var sorted = [];
            for(var key in dict) {
                sorted[sorted.length] = key;
            }
            sorted.sort();
            var tempDict = {};
            for(var i = 0; i < sorted.length; i++) {
                tempDict[sorted[i]] = dict[sorted[i]];
            }
            return tempDict;
        }

    </script>

@endsection
