const map = L.map('map').fitBounds([
    [{{ $bounds['maxLat'] }}, {{ $bounds['maxLng'] }}],
    [{{ $bounds['minLat'] }}, {{ $bounds['minLng'] }}]
])

const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map)

@isset ($hereMapApiKey)
    const here = {apiKey: '{{ $hereMapApiKey }}'}
    const hereHybrid = L.tileLayer(`https://{m}.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/hybrid.day/{z}/{x}/{y}/512/png8?apiKey=${here.apiKey}&pois=true&ppi=320`, {m: Math.floor(Math.random() * 3 + 1)})
    const hereStreet = L.tileLayer(`https://{m}.base.maps.ls.hereapi.com/maptile/2.1/maptile/newest/normal.day.transit/{z}/{x}/{y}/512/png8?apiKey=${here.apiKey}&pois=true&ppi=320`, {m: Math.floor(Math.random() * 3 + 1)})
    const hereTerrain = L.tileLayer(`https://{m}.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/terrain.day/{z}/{x}/{y}/512/png8?apiKey=${here.apiKey}&pois=true&ppi=320`, {m: Math.floor(Math.random() * 3 + 1)})
@endif

@isset ($googleApiKey)
    const gHybrid = L.gridLayer.googleMutant({type: 'hybrid'})
    const gRoadmap = L.gridLayer.googleMutant({type: 'roadmap'})
    const gTerrain = L.gridLayer.googleMutant({type: 'terrain'})
@endif

let baseLayers = {
    "Open Street Maps": osm,
    @isset ($hereMapApiKey)
        "Here Maps Hybrid": hereHybrid,
        "Here Maps Street Map": hereStreet,
        "Here Maps Terrain": hereTerrain,
    @endif
    @isset ($googleApiKey)
        "Google Hybrid": gHybrid,
        "Google Roadmap": gRoadmap,
        "Google Terrain": gTerrain,
    @endif
}
