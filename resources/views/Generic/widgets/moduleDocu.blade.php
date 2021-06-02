<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<div class="widget widget-stats bg-aqua-darker">
  {{-- info/data --}}
  <div class="stats-info text-center">

    @if (isset($urls['documentation']))
      {!! HTML::decode (HTML::link($urls['documentation'],
        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
          <i style="font-size: 25px;" class="img-center fa fa-question-circle p-10"></i><br />
          <span class="username text-ellipsis text-center">'.trans('view.dashboard.docu').'</span>
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
        <span class="username text-ellipsis text-center">'.trans('view.dashboard.requestHelp').'</span>
      </span>'))
    !!}

  </div>
  {{-- reference link --}}
  <div class="stats-link noHover"><a href="#">{{ trans('view.dashboard.help') }}</a></div>
</div>
