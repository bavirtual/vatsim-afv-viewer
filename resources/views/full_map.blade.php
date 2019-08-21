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

        var map = L.map('map', { attributionControl: false, scrollWheelZoom: false, dragging: false, zoomControl:false }).setView([0, 0], 2);
        {{--var stations = @json($data);--}}
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        	subdomains: 'abcd',
        	maxZoom: 19
        }).addTo(map);
    </script>
</body>
</html>