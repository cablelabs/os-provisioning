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

/* Keep Sidebar open and Save State and Minify Status of Sidebar
*  @author: Christian Schramm
*/
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
	}
    $('#sidebar ul > li').click(function (event) {
        localStorage.setItem("sidebar-item", $(this).attr('id'));
        localStorage.setItem("clicked-item", $(this).attr('id'));
    });

    $('#sidebar ul > li > div > .caret-link').click(function (event) {
        var li_item = $(this).closest('[data-sidebar=level1]');
        if(li_item.hasClass('expand')){
            li_item.removeClass('expand');
            li_item.children('.sub-menu').css('display', 'none');
        }else {
            li_item.children('.sub-menu').css('display', 'block');
            li_item.addClass('expand');
        }
		});

    $('#sidebar .sub-menu  li').click(function (event) {
        event.stopPropagation();
        localStorage.setItem("sidebar-item", $(this).closest('[data-sidebar=level1]').attr('id'));
        localStorage.setItem("clicked-item", $(this).attr('id'));
    });

} else {
  console.log("sorry, no Web Storage Support - Cant save State of Sidebar -please update your Browser")
}




/* This bit can be used on the entire app over all pages and will work for both tabs and pills.
* Also, make sure the tabs or pills are not active by default,
* otherwise you will see a flicker effect at page load.
* Important: Make sure the parent ul has an id. Thanks Alain
* http://stackoverflow.com/posts/16984739/revisions
*/
var saveTabPillState = function() {
  // Show tab from hash
  // Note: for an URL with hash the function above will not be initialised and therefore will not save the tab state
  if (window.location.hash) {
    return $(window.location.hash + 'tab').tab('show');
  }

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
      return $("#" + containerId + " a[href='" + href + "']").tab('show');
    });

    $("ul.nav.nav-pills, ul.nav.nav-tabs").each(function() {
      var $this = $(this);
      if (!json[$this.attr("id")]) {
        return $this.find("a[data-toggle=tab]:first, a[data-toggle=pill]:first").tab("show");
      }
    });
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

function rezizeTextareas() {
  $('textarea').each(function () {
    this.setAttribute('style', 'height:' + (this.scrollHeight + 5) + 'px;max-height: 1080px;');
  }).on('input', function () {
    var scrollLeft = window.pageXOffset ||
      (document.documentElement || document.body.parentNode || document.body).scrollLeft;
    var scrollTop = window.pageYOffset ||
      (document.documentElement || document.body.parentNode || document.body).scrollTop;
    this.style.height = "auto";
    this.style.height = (this.scrollHeight + 10) + 'px';
    window.scrollTo(scrollLeft, scrollTop);
  });
}

/*
 * Init form range slider
 */
function rangeSlider(argument) {
  $("#slider").ionRangeSlider({
    type: 'single',
    grid: true,
  });
}

var NMS = function () {
	"use strict";

	return {
		//main function
		init: function () {
			makeNavbarSearch();
			makeInputFitOnResize();
			saveTabPillState();
      positionErdPopover();
      rezizeTextareas();
      rangeSlider();
		},
  };
}();
