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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script type="text/javascript">
        firstLoad = true;
        pilotRings = true;
        atcRings = true;
        voiceOnly = false;

        var map = L.map('map').setView([0, 0], 2);

        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        	subdomains: 'abcd',
        	maxZoom: 19
        }).addTo(map);
    </script>
</body>
</html>