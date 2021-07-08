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
<!-- ================== BEGIN BASE JS ================== -->
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-3.2.0.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-migrate-1.4.1.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/Abilities/axios.min.js')}}"></script>

{{-- When in Development use this Version
    <script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
--}}

<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/Abilities/axios.min.js')}}"></script>

{{-- When in Development use this Version
  <script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
--}}

<script src="{{asset('components/assets-admin/plugins/bootstrap4/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2/dist/js/i18n/'.App::getLocale().'.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/main/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/main/media/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/responsive-bootstrap/js/responsive.bootstrap4.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/fixedHeader/js/dataTables.fixedHeader.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/fixedHeader-bootstrap/js/fixedHeader.bootstrap4.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/chart/Chart.min.js')}}"></script>

<!--[if lt IE 9]>
  <script src="{{asset('components/assets-admin/crossbrowserjs/html5shiv.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/respond.min.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/excanvas.min.js')}}"></script>
<![endif]-->
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons-bootstrap/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.colVis.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/ionRangeSlider/js/ion.rangeSlider.js')}}"></script>


<script src="{{asset('components/assets-admin/js/apps.js')}}"></script>
<script src="{{asset('components/nmsprime.js')}}"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script language="javascript">
/*
 * global document ready function
 */
$(document).ready(function() {
  App.init();
  NMS.init();
  {{-- init modals --}}
  $("#alertModal").modal();
});
Vue.directive('hover-class', {
  bind(el, binding, vnode) {
    const { value="" } = binding;
    el.addEventListener('mouseenter',()=> {
        el.classList.add(value)
    });
    el.addEventListener('mouseleave',()=> {
        el.classList.remove(value)
    });
  },
  unbind(el, binding, vnode) {
    el.removeEventListener('mouseenter');
    el.removeEventListener('mouseleave')
  }
})

new Vue({
  el: '#sidebar',
  mounted() {
    if (typeof(Storage) === "undefined") {
      console.error("Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser")
    }

    this.initSidebar()
  },
  data() {
    return {
      {{-- sidebarObject: @json($view_header_links), --}}
      {{-- route: '{{$route_name}}', --}}
      minified: null,
      isVisible: true,
      isSearchMode: false,
      isCollapsed: true,
      scrollheight: '50px',
      lastActive: 'null',
      lastClicked: 'null',
      activeItem: 'null',
      clickedItem: 'null'
    }
  },
  methods: {
    initSidebar() {
      this.handleMinify()

      this.isVisible = localStorage.getItem('sidebar-net-visibility') === 'true'
      this.lastActive = this.activeItem = localStorage.getItem('sidebar-item')
      this.lastClicked = this.clickedItem = localStorage.getItem('clicked-item')
      this.isCollapsed = false
    },
    handleMinify() {

      if (this.minified) {
        return $('#page-container').addClass('page-sidebar-minified')
      }

      $('#page-container').removeClass('page-sidebar-minified')
    },
    setVisibility() {
      this.isVisible = !isVisible

      localStorage.setItem('sidebar-net-visibility', JSON.stringify(this.isVisible))
    },
    setMenu(name) {
      if (name === this.activeItem) {
        return this.isCollapsed = ! this.isCollapsed
        {{-- this.clickedItem = 'null'; localStorage.setItem("clicked-item", this.clickedItem) --}}
      }

      this.activeItem = name
      this.clickedItem = name
      this.isCollapsed = false

      localStorage.setItem("sidebar-item", name)
      localStorage.setItem("clicked-item", name)
    },
    setSubMenu(name) {
      this.clickedItem = name
      localStorage.setItem("clicked-item", name)
    },
    beforeEnter(el) {
      el.style.maxHeight = '0'
    },
    enter(el) {
      el.style.maxHeight = el.scrollHeight + 'px'
    },
    beforeLeave(el) {
      el.style.maxHeight = el.scrollHeight + 'px'
      el.classList.add('accordion-leave-active')
    },
    leave(el) {
      el.classList.add('accordion-leave-active')
      console.log('leave')
      el.style.maxHeight = '0'
    },
  }
})

</script>
