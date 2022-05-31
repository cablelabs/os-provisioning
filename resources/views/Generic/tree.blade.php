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
@extends ('Layout.split84-nopanel')

@section('content_top')

    <li class="active"><a href={{route($route_name.'.index')}}>
    {!!\App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null) !!}
    {{ trans_choice('view.Header_'.$route_name, 2) }}</a>
    </li>

@stop

@section('content_left')

    @DivOpen(12)

        <h1 class="page-header">
        {!!\App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null) !!}
        {{ trans_choice('view.Header_'.$route_name, 2) }}
        </h1>

        {{-- we also use this blade for showing the firmware distribution, which doesn't use models --}}
        @if (isset($view_var[0]))
            <div class="btn pull-right">
                @include('Generic.documentation', ['model' => $view_var[0]])
            </div>
        @endif

        @if ($create_allowed)
            {!! Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) !!}
                <button class="btn btn-primary m-b-15" style="simple">
                    <i class="fa fa-plus fa-lg m-r-10" aria-hidden="true"></i>
                    {{ trans("view.Button_Create {$route_name}") }}
                </button>
            {!! Form::close() !!}
        @endif
    @DivClose()

    {{-- database entries inside a form with checkboxes to be able to delete one or more entries --}}
    @DivOpen(12)
        {!! Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete', 'onsubmit' => 'return submitMe()')) !!}
            @if (!is_array($view_var))
                @include('Generic.tree_hidden_helper', array('items' => $view_var))
            @endif

            <div id="jstree-default">
                @include('Generic.tree_item', array('items' => $view_var, 'color' => 0))
            </div>

            {{-- delete/submit button of form --}}
            <button class="btn btn-danger btn-primary m-r-5 m-t-15" style="simple">
                    <i class="fa fa-trash-o fa-lg m-r-10" aria-hidden="true"></i>
                    {{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button') }}
            </button>
        {!! Form::close() !!}
    @DivClose()

@stop

@section('javascript')
<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>

<script>

    /**
     * Generate jsTree
     *
     * Documentation: https://www.jstree.com/demo/
     *
     * @author: Christian Schramm
     */
    var makeJsTreeView = function() {
      $('#jstree-default').jstree({
          'plugins': [ "html_data", "checkbox", "wholerow", "types", "ui", "search", "state"],
          "core": {
              "dblclick_toggle": true,
              "themes": {
                  "responsive": true,
              }
          },
          "checkbox": {
              "cascade": "",
              "three_state": false,
              "whole_node" : false,
              "tie_selection" : false,
              "real_checkboxes": true
          },
          "state" : {
                "filter" : function (k) { delete k.core.selected; return k; },
                "key"   : "tree-{!! $route_name !!}",
                'ttl' : false,
            },
          "types": {
              "cm":{
                "icon": "fa fa-hdd-o text-warning fa-lg"
              },
              "mta": {
                "icon": "fa fa-fax text-info fa-lg"
              },
              "1": { // Net
                "icon": "fa fa-cloud text-info fa-lg"
              },
              "2": { // Cluster
                "icon": "fa fa-soundcloud text-warning fa-lg"
              },
              "3": { // NetGw
                "icon": "fa fa-building text-success fa-lg"
              },
              "4": { // Amplifier
                "icon": "fa fa-toggle-right text-danger fa-lg"
              },
              "5": { // Node
                "icon": "fa fa-arrow-circle-o-right text-warning fa-lg"
              },
              "6": { // Data
                "icon": "fa fa-database text-active fa-lg"
              },
              "7": { // UPS
                "icon": "fa fa-stack fa-bolt text-warning fa-lg"
              },
              "8": { // Tap
                "icon": "fa fa-code-fork text-info fa-lg"
              },
              "9": { // Tap-Port
                "icon": "fa fa-dot-circle-o text-info fa-lg"
              },
              "10": { // NMSPrime ProvHA slave
                "icon": "fa fa-server text-secondary fa-lg"
              },
              "15": { // RKM-Server
                "icon": "fa fa-server text-warning fa-lg"
              },
              "16": { // Market
                "icon": "fa fa-cart-plus text-success fa-lg"
              },
              "17": { // Hub
                "icon": "fa fa-forumbee text-success fa-lg"
              },
              "18": { // CCAP Core
                "icon": "fa fa-building text-danger fa-lg"
              },
              "19": { // Core Leaf
                "icon": "fa fa-leaf text-success fa-lg"
              },
              "20": { // Spine
                "icon": "fa fa-support text-info fa-lg"
              },
              "21": { // Node Leaf
                "icon": "fa fa-leaf text-secondary fa-lg"
              },
              "22": { // RPD
                "icon": "fa fa-toggle-right text-warning fa-lg"
              },
              "23": { // CPE
                "icon": "fa fa-hdd-o text-info fa-lg"
              },
              "default": {
                  "icon": "fa fa-file-code-o text-success fa-lg"
              },
              "default-2": {
                  "icon": "fa fa-file-excel-o text-warning fa-lg"
              },
              "default-3": {
                  "icon": "fa fa-file-pdf-o text-danger fa-lg"
              },
              "default-4": {
                  "icon": "fa fa-file-image-o text-info fa-lg"
              }
          }
      });


      $('#jstree-default').on('select_node.jstree', function(e,data) {
          var link = data.node.a_attr.href;
          if (link != "#" && link != "javascript:;" && link != "") {
              document.location.href = link;
              return false;
          }
      });


    // trigger on Checkbox change and give
    // invisible form the name of selected id
    // @author: Christian Schramm

      $('#jstree-default').on("check_node.jstree uncheck_node.jstree", function (e, data) {
          if (data.node.state.checked) {
            document.getElementById('myField'+ data.node.id).name = data.node.id;
          } else {
            document.getElementById('myField'+ data.node.id).name = '';
          }
      });
    };


    $(document).ready(function() {
        makeJsTreeView();
    });

</script>
@stop
