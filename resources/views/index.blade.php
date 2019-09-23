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
                        <button class="btn btn-sm" id="toggleVoiceOnly">Toggle VOI Only</button>
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
        voiceOnly = false;

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
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
             attribution: '&copy; ' + '<a href="http://www.esri.com/">Esri</a>'
        });
        dark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        	subdomains: 'abcd'
        });
        var maps = {"Basic": basic, "Streets": streets, "Satellite": satellite, "Dark": dark};
        L.control.layers(maps).addTo(map);

        markers = [];
        frequencyList = [];

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

        $('#toggleVoiceOnly').click(function () {
            if(voiceOnly == true) {
                $('path[stroke="#00eaff"]').hide();
                voiceOnly = false;
            } else {
                $('path[stroke="#00eaff"]').show();
                voiceOnly = true;
            }
        });

        reloadMapData();
        setInterval(function() {
            reloadMapData();
        }, 15000);


        function reloadMapData() {
            $.ajax({
                type: 'get',
                url: '{{ route('atis-map-data') }}',
                success: function(data) {
                    for (var i = 0; i < markers.length; i++) {
                        map.removeLayer(markers[i]);
                    }

                    markers = [];
                    frequencyList = {};
                    $('path.leaflet-interactive').remove(); // Remove ranges
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

                        frequencyList[callsign] = {};
                        frequencyList[callsign]['frequencies'] = [];
                        frequencyList[callsign]['onFSD'] = true;
                        var transceivers = data.pilots[callsign].transceivers;
                        transceivers.forEach(function(transceiver){
                            if (transceiver.heightMslM <= 0){
                                transceiver.heightMslM = 50;
                            }
                            var frequency = (transceiver.frequency/1000000).toFixed(3);
                            var RadiusMeters = 4193.18014745372 * Math.sqrt(transceiver.heightMslM);
                            if(frequency == 122.800 && RadiusMeters > 27780){ // UNICOM Max Range = 15nm => 27780m
                                RadiusMeters = 27780;
                            }
                            //ranges.push(L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#ce6262', weight: 1}).bindPopup(content).addTo(map));
                            L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#ce6262', weight: 1}).bindPopup(content).addTo(map);

                            if(!frequencyList[callsign]['frequencies'].includes(frequency)) {
                                frequencyList[callsign]['frequencies'].push(frequency);
                            }
                        });
                        frequencyList[callsign]['fsdFreq'] = null;
                    }

                    for(callsign in data.controllers){
                        var content = '<b>' + callsign + '</b><br>';

                        lat = data.controllers[callsign].latitude;
                        lon = data.controllers[callsign].longitude;

                        if(! callsign.includes('_ATIS')){
                            content += data.controllers[callsign].frequency + '<br>';
                            content += data.controllers[callsign].member.name + '<br>';
                            markers.push(L.marker([lat, lon], {
                                icon: L.icon({
                                    iconUrl: '{{ asset_path('img/map/atc.png') }}',
                                    iconSize: [10, 18],
                                    iconAnchor: [5, 18],
                                    popupAnchor: [0, -18]
                                })
                            }).bindPopup(content).addTo(map));
                        } else {
                            content += data.controllers[callsign].frequency + '<br>';
                            markers.push(L.marker([lat, lon], {
                                icon: L.icon({
                                    iconUrl: '{{ asset_path('img/map/weather.png') }}',
                                    iconSize: [20, 12.05],
                                    iconAnchor: [10, 6.025],
                                    popupAnchor: [5, -6.025]
                                })
                            }).bindPopup(content).addTo(map));
                        }


                        frequencyList[callsign] = {};
                        frequencyList[callsign]['frequencies'] = [];
                        frequencyList[callsign]['onFSD'] = true;
                        var transceivers = data.controllers[callsign].transceivers;
                        transceivers.forEach(function(transceiver){
                            if (transceiver.heightMslM <= 0){
                                transceiver.heightMslM = 50;
                            }
                            var frequency = (transceiver.frequency/1000000).toFixed(3);
                            var content = '<b>' + callsign + '</b><br>';
                            if(! callsign.includes('_ATIS')){
                                content += data.controllers[callsign].member.name + '<br>';
                            }
                                content += frequency + '<br>';

                            var RadiusMeters = 4193.18014745372 * Math.sqrt(transceiver.heightMslM);
                            if(frequency == 122.800 && RadiusMeters > 27780){ // UNICOM Max Range = 15nm => 27780m
                                RadiusMeters = 27780;
                            }
                            //ranges.push(L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#418041', weight: 1}).bindPopup(content).addTo(map));
                            L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#418041', weight: 1}).bindPopup(content).addTo(map);
                            if(!frequencyList[callsign]['frequencies'].includes(frequency)) {
                                frequencyList[callsign]['frequencies'].push(frequency);
                            }
                        });
                        frequencyList[callsign]['fsdFreq'] = data.controllers[callsign].frequency;
                    }

                    for(callsign in data.other){
                        frequencyList[callsign] = {};
                        frequencyList[callsign]['frequencies'] = [];
                        frequencyList[callsign]['onFSD'] = false;
                        var transceivers = data.other[callsign].transceivers;
                        transceivers.forEach(function(transceiver){
                            if (transceiver.heightMslM <= 0){
                                transceiver.heightMslM = 50;
                            }
                            var frequency = (transceiver.frequency/1000000).toFixed(3);
                            var content = '<b>' + callsign + '</b><br>';
                            if(callsign.includes('_ATIS')){
                                content += frequency + '<br>';
                                markers.push(L.marker([transceiver.latDeg, transceiver.lonDeg], {
                                    icon: L.icon({
                                        iconUrl: '{{ asset_path('img/map/weather.png') }}',
                                        iconSize: [20, 12.05],
                                        iconAnchor: [10, 6.025],
                                        popupAnchor: [5, -6.025]
                                    })
                                }).bindPopup(content).addTo(map));
                            }
                            var RadiusMeters = 4193.18014745372 * Math.sqrt(transceiver.heightMslM);
                            if(!frequencyList[callsign]['frequencies'].includes(frequency)) {
                                frequencyList[callsign]['frequencies'].push(frequency);
                            }
                            L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#00eaff', weight: 1}).bindPopup(content).addTo(map);
                        });
                        frequencyList[callsign]['fsdFreq'] = null;
                    }


                    if(pilotRings == 0) {
                        $('path[stroke="#ce6262"]').hide();
                    }
                    if(atcRings == 0) {
                        $('path[stroke="#418041"]').hide();
                    }
                    if(voiceOnly == 0) {
                        $('path[stroke="#00eaff"]').hide();
                    }

                    if(markers.length > 0 && firstLoad == true) {
                        map.fitBounds(L.featureGroup(markers).getBounds());
                        firstLoad = false;
                    }

                    frequencyList = sortOnKeys(frequencyList);
                    for(callsign in frequencyList){
                        var details = '';
                        if (frequencyList[callsign].onFSD == true){
                            if(frequencyList[callsign].frequencies.length > 0){
                                details += frequencyList[callsign].frequencies.join(', ');
                            } else if (frequencyList[callsign].fsdFreq != null) {
                                details += frequencyList[callsign].fsdFreq + ' [TXT Only]';
                            } else {
                                details += 'No frequency';
                            }
                        } else {
                            if(frequencyList[callsign].frequencies.length > 0){
                                details += frequencyList[callsign].frequencies.join(', ') + ' [VOI only]';
                            } else {
                                details += 'No frequency';
                            }
                        }
                        $('#atis-list').append('<h5 style="margin-bottom: 0;"><b>' + callsign + '</b> - ' + details + '</h5>');
                    }

                    if (Object.keys(frequencyList).length == 0){
                        $('#atis-list').append('<h5 style="margin-bottom: 0;">No Clients</h5>');
                        $('#online-count').html('0 Clients Connected');
                    } else {
                        $('#online-count').html(Object.keys(frequencyList).length + ' Clients Connected');
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
