<!-- Get users geoposition when last update was more than 10 min ago -->
@if (\Module::collections()->has('Workforce') && (time() - strtotime(\Auth::user()->geopos_updated_at)) > 10*60)

<script>

    function updatePos(pos)
    {
        $.ajax({
            type: 'post',
            url: '{{ route("user.updateGeopos") }}',
            timeout: 500,
            data: {
                _token: "{{\Session::get('_token')}}",
                id: '{{ \Auth::user()->id }}',
                x: pos.coords.longitude,
                y: pos.coords.latitude,
            },
        });
    }

    // https://www.w3schools.com/html/html5_geolocation.asp
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(updatePos);
    }

</script>

@endif
