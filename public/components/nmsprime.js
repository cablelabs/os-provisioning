// Own JS NMS Prime File for our specific functions

// Type anywhere to search in global search for keyword
// Escape Keymodifiers and exit with escape
// @author: Christian Schramm
var makeNavbarSearch = function() {
	$('#togglesearch').on('click', function (event) {
	  $("#globalsearch").focus().select();
	});

	$(document).on('keypress', function (event) {
	  if ($('*:focus').length == 0 && event.target.id != 'globalsearch'){
	    var code = (event.keyCode ? event.keyCode : event.which);
	    if (event.which !== 0 && !event.ctrlKey && !event.metaKey && !event.altKey) {
	      $("#togglesearch").click();
	      if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
	        $("#globalsearch").val(String.fromCharCode(code));
	        }
	      }
	    }
	});

	$('#globalsearch').on('keydown', function (event) {
	    var code = (event.keyCode ? event.keyCode : event.which);
	    if (code == 27) {
	      $("#globalsearch").val('');
	      $("#header").removeClass('navbar-search-toggled');
	      $("#globalsearch").removeClass('navbar-search-toggled');
	      $("#globalsearch").blur();
	    }
	});
};

// Keep Sidebar open and Save State and Minify Status of Sidebar
// @author: Christian Schramm
if (typeof(Storage) !== "undefined") {
    //save minified s_state
    var ministate = localStorage.getItem("minified-state");
    var sitem = localStorage.getItem("sidebar-item");
    var chitem = localStorage.getItem("clicked-item");

    if (ministate == "true") {
    $('#page-container').addClass('page-sidebar-minified');
    } else {
    $('#page-container').removeClass('page-sidebar-minified');
    }

    if (!$('#dashboardsidebar').hasClass('active')) {
        $('#' + sitem).addClass("expand");
        $('#' + sitem).addClass("active");
        $('#' + sitem + ' .sub-menu ').css("display", "block");
        $('#' + chitem).addClass("active");

        $('#sidebar .sub-menu li').click(function(event) {
            localStorage.setItem("clicked-item", $(this).attr('id'));
            localStorage.setItem("sidebar-item", $(this).parent().parent().attr('id'))
        });
    }
} else {
  console.log("sorry, no Web Storage Support - Cant save State of Sidebar")
}




/* This bit can be used on the entire app over all pages and will work for both tabs and pills.
* Also, make sure the tabs or pills are not active by default,
* otherwise you will see a flicker effect at page load.
* Important: Make sure the parent ul has an id. Thanks Alain
* http://stackoverflow.com/posts/16984739/revisions
*/
var saveTabPillState = function() {
  $(function() {
    var json, tabsState;
    $('a[data-toggle="pill"], a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      var href, json, parentId, tabsState;

      tabsState = localStorage.getItem("tabs-state");
      json = JSON.parse(tabsState || "{}");
      parentId = $(e.target).parents("ul.nav.nav-pills, ul.nav.nav-tabs").attr("id");
      href = $(e.target).attr('href');
      json[parentId] = href;

      return localStorage.setItem("tabs-state", JSON.stringify(json));
    });

    tabsState = localStorage.getItem("tabs-state");
    json = JSON.parse(tabsState || "{}");

    $.each(json, function(containerId, href) {
      return $("#" + containerId + " a[href=" + href + "]").tab('show');
    });

    $("ul.nav.nav-pills, ul.nav.nav-tabs").each(function() {
      var $this = $(this);
      if (!json[$this.attr("id")]) {
        return $this.find("a[data-toggle=tab]:first, a[data-toggle=pill]:first").tab("show");
      }
    });
  });
};


// Generate jsTree
// @author: Christian Schramm
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
      "state" : { "filter" : function (k) { delete k.core.selected; return k; } },
      "types": {
          "cm":{
            "icon": "fa fa-hdd-o text-warning fa-lg"
          },
          "mta": {
            "icon": "fa fa-fax text-info fa-lg"
          },
          "Net": {
            "icon": "fa fa-cloud text-info fa-lg"
          },
          "Cluster": {
            "icon": "fa fa-soundcloud text-warning fa-lg"
          },
          "Cmts": {
            "icon": "fa fa-building text-success fa-lg"
          },
          "Amplifier": {
            "icon": "fa fa-toggle-right text-danger fa-lg"
          },
          "Node": {
            "icon": "fa fa-arrow-circle-o-right text-warning fa-lg"
          },
          "Data": {
            "icon": "fa fa-server text-active fa-lg"
          },
          "default": {
              "icon": "fa fa-file-code-o text-success fa-lg"
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

// Select2 Init - intelligent HTML select
// Resize on Zoom to look always pretty
// @author: Christian Schramm
var makeInputFitOnResize = function() {
  $(window).resize(function() {
  $('.select2').css('width', "100%");
  });
  $("select").select2();
};

var positionErdPopover= function() {
$('.erd-popover').mousemove(
  function(event){
    var mouseX = event.pageX + 20;
    var mouseY = event.pageY;
    if ($(this).attr('shape') == "circle") {
      var mouseY = event.pageY -50;
    }
    $('.popover').css({'top':mouseY,'left':mouseX}).fadeIn('slow');
    $('.popover .arrow').css({'top': ($('.popover').height()/2) ,'left':-10});
    $(".popover").show();
});
};

/*
 * Table on-hover click
 * NOTE: This automatically adds on-hover click to all table 'td' elements which are in class 'ClickableTd'.
 *       Please note that the table needs to be in class 'table-hover' for visual marking.
 *
 * HOWTO:
 *  - If clicked on td element which is assigned in class ClickableTd the function bellow is called.
 *  - fetch parent element of td element, which should/(must?) be a row.
 *  - search in tr HTML code for an HTML "a" element and fetch the href attribute
 * INFO: - working directly with row element also adds a click object to checkbox entry, which disabled checkbox functionality
 */
$('.datatable, .clickableRow').click(function (e) {
  if ($(e.target).hasClass('ClickableTd') && $(e.target).is('td')) {
    window.location = $(e.target.parentNode).find('a').attr("href");
  }
  if ($(e.target).hasClass('index_check') && !($(e.target).find('input:checkbox').is(':disabled')) ) {
    var checkbox = $(e.target).find('input:checkbox');
    checkbox.prop('checked',!checkbox.prop("checked") );
  }
  if (e.target.id == 'selectall' || e.target.id =="allCheck") {
    var allCheck = ($(this).closest('table').find('td input:checkbox:enabled'));
    allCheck.prop('checked', !allCheck.prop("checked"));
  }
});


$('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
  $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
});


var NMS = function () {
	"use strict";

	return {
		//main function
		init: function () {
			makeNavbarSearch();
			makeInputFitOnResize();
			saveTabPillState();
			makeJsTreeView();
      positionErdPopover();
		},
  };
}();
