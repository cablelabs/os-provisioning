@if (isset($includes) && in_array('css', $includes))
<link rel="stylesheet" href="{{asset('css/leaflet/leaflet.css')}}"/>
<link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.draw.css') }}"/>
@endif

@if (isset($includes) && in_array('js', $includes))
<script src="{{ asset('js/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet-heat.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.draw.js') }}"></script>
<script src="{{ asset('js/leaflet/pixi.js') }}"></script>
<script src="{{ asset('js/leaflet/L.PixiOverlay.js') }}"></script>
<script src="{{ asset('js/components/assets-admin/pixi-overlay-tools.js') }}"></script>

@if (config('app.googleApiKey'))
    <script src="{{ asset('js/leaflet/Leaflet.GoogleMutant.js') }}"></script>
    <script async defer src="https://maps.google.com/maps/api/js?key={{ config('app.googleApiKey') }}"></script>
@endif
@endif
