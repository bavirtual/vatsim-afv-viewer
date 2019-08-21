<html>
<head>
    <link rel="stylesheet" type="text/css" href="{{ app_asset_path('vendors/leaflet/leaflet.css') }}">
    <script type="text/javascript" src="{{ app_asset_path('vendors/leaflet/leaflet.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/leaflet/leaflet-geodesic.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/leaflet/leaflet-rotation.js') }}"></script>
</head>
<body style="width:100%;height:100%;margin:0;padding:0;">
    <div id="map" style="min-height: 100%;min-width:100%;"></div>

    <script type="text/javascript">
        var map = L.map('map', { attributionControl: false, scrollWheelZoom: false, dragging: false, zoomControl: false }).setView([0, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        	subdomains: 'abcd',
        	maxZoom: 8,
        }).addTo(map);

        var transceivers = @json($data);

        var circles = [];
        
        transceivers.forEach(function(transceiver){
            var RadiusMeters = 4193.18014745372 * Math.sqrt(transceiver.altMslM);
            circles.push(L.circle([transceiver.latDeg, transceiver.lonDeg], {radius: RadiusMeters, fillOpacity: .2, color: '#00ffff', weight: 0}).addTo(map));
        });

        map.fitBounds(L.featureGroup(circles).getBounds());
    </script>
</body>
</html>