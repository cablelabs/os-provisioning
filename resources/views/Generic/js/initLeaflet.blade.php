const map = L.map('map', {
    zoomSnap: 0.25,
    zoomDelta: 0.5,
    wheelPxPerZoomLevel: 150
})

@if (isset($bounds['maxLat']) && $bounds['maxLng'] && $bounds['minLat'] && $bounds['minLng'])
    map.fitBounds([
        [{{ $bounds['maxLat'] }}, {{ $bounds['maxLng'] }}],
        [{{ $bounds['minLat'] }}, {{ $bounds['minLng'] }}]
    ])
@else
    map.setView(new L.LatLng(50.82,12.928), 16);
    var marker = L.marker([50.81987, 12.9276208]).addTo(map);
    var popup = marker.bindPopup('{!! trans('view.map.NMS Prime') !!}');
    alert('{{ trans('view.map.noData') }}')
@endif

const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map)

@if (config('app.hereMapApiKey'))
    const here = {apiKey: '{{ config('app.hereMapApiKey') }}'}
    const hereHybrid = L.tileLayer(`https://{m}.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/hybrid.day/{z}/{x}/{y}/512/png8?apiKey=${here.apiKey}&pois=true&ppi=320`, {m: Math.floor(Math.random() * 3 + 1)})
    const hereStreet = L.tileLayer(`https://{m}.base.maps.ls.hereapi.com/maptile/2.1/maptile/newest/normal.day.transit/{z}/{x}/{y}/512/png8?apiKey=${here.apiKey}&pois=true&ppi=320`, {m: Math.floor(Math.random() * 3 + 1)})
    const hereTerrain = L.tileLayer(`https://{m}.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/terrain.day/{z}/{x}/{y}/512/png8?apiKey=${here.apiKey}&pois=true&ppi=320`, {m: Math.floor(Math.random() * 3 + 1)})
@endif

@if (config('app.googleApiKey'))
    const gHybrid = L.gridLayer.googleMutant({type: 'hybrid'})
    const gRoadmap = L.gridLayer.googleMutant({type: 'roadmap'})
    const gTerrain = L.gridLayer.googleMutant({type: 'terrain'})
@endif

let baseLayers = {
    "Open Street Maps": osm,
    @if (config('app.hereMapApiKey'))
        "Here Maps Hybrid": hereHybrid,
        "Here Maps Street Map": hereStreet,
        "Here Maps Terrain": hereTerrain,
    @endif
    @if (config('app.googleApiKey'))
        "Google Hybrid": gHybrid,
        "Google Roadmap": gRoadmap,
        "Google Terrain": gTerrain,
    @endif
}
