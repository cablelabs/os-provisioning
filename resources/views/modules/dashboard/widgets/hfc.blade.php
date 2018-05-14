<style>
    a:hover {
        text-decoration: none;
    }
</style>

<div class="widget widget-stats bg-blue">
    {{-- info/data --}}
    <div class="stats-info d-flex">
        <div class="btn btn-dark m-5 m-r-10">
            {!! HTML::decode (HTML::link('https://'.\Request::server('HTTP_HOST').'/cacti',
                '<h3><div class="text-center"><i style="color: white;" class="img-center fa fa-tachometer"></i></div></h3>
                <div style="color: white;" class="username text-ellipsis text-center">Cacti System</div>', ['target' => '_blank']))
            !!}
        </div>
        <div class="btn btn-dark m-5 m-r-10 m-l-10">
            {!! HTML::decode (HTML::link('https://'.\Request::server('HTTP_HOST').'/icingaweb2',
                '<h3><div class="text-center"><i style="color: white;" class="img-center fa fa-info-circle"></i></div></h3>
                <div style="color: white;" class="username text-ellipsis text-center">Icinga2 System</div>', ['target' => '_blank']))
            !!}
        </div>
    </div>
    {{-- reference link --}}
    <div class="stats-link"><a href="#">External</a></div>
</div>

