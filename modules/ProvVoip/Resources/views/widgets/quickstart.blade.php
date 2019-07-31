<style>
    a:hover {
        text-decoration: none;
    }
</style>

<div class="widget widget-stats bg-grey">
    {{-- info/data --}}
    <div class="stats-info text-center">
        {!! HTML::decode (HTML::linkRoute('PhoneTariff.create',
            '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                <i style="font-size: 25px;" class="img-center fa fa-phone-square p-10"></i><br />
                <span class="username text-ellipsis text-center">'.trans_choice('view.Button_Create PhoneTariff', 1).'</span>
            </span>'))
        !!}
    </div>
    {{-- reference link --}}
    <div class="stats-link"><a href="#">{{ trans('view.Dashboard_Quickstart') }}</a></div>
</div>
