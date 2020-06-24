<div class="widget widget-stats bg-aqua-darker">
  {{-- info/data --}}
  <div class="stats-info text-center">

    @if (isset($urls['documentation']))
      {!! HTML::decode (HTML::link($urls['documentation'],
        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
          <i style="font-size: 25px;" class="img-center fa fa-question-circle p-10"></i><br />
          <span class="username text-ellipsis text-center">'.trans('view.Dashboard_Docu').'</span>
        </span>',['target' => '_blank']))
      !!}
    @endif

    @if (isset($urls['youtube']))
      {!! HTML::decode (HTML::link($urls['youtube'],
        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
          <i style="font-size: 25px;" class="img-center fa fa-tv p-10"></i><br />
          <span class="username text-ellipsis text-center">Youtube</span>
        </span>', ['target' => '_blank']))
      !!}
    @endif

    @if (isset($urls['forum']))
      {!! HTML::decode (HTML::link($urls['forum'],
        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
          <i style="font-size: 25px;" class="img-center fa fa-wpforms p-10"></i><br />
          <span class="username text-ellipsis text-center">Forum</span>
        </span>', ['target' => '_blank']))
      !!}
    @endif

    {!! HTML::decode (HTML::linkRoute('SupportRequest.index',
      '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
        <i style="font-size: 25px;" class="img-center fa fa-envelope-open p-10"></i><br />
        <span class="username text-ellipsis text-center">'.trans('view.Dashboard_RequestHelp').'</span>
      </span>'))
    !!}

  </div>
  {{-- reference link --}}
  <div class="stats-link noHover"><a href="#">{{ trans('view.Dashboard_Help') }}</a></div>
</div>
