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
	      if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
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

$('.jsDateInterval').each(function () {
  $(this.parentNode).addClass('d-flex')
  $(this).addClass('h-100')
  $(this).after(`
    <div class="btn btn-secondary ml-2 jsToggle jsDate"><i class="fa mr-0 fa-calendar"></i></div>
    <div class="btn jsToggle jsInterval"><i class="fa mr-0 fa-hourglass-o"></i></div>
  `)
})

$('.jsToggle').click(function (event) {
  let jqEl = $(this)
  if (jqEl.hasClass('btn-secondary')) {
    return
  }

  let jqInputgroup =  jqEl.siblings()
  let input = jqInputgroup.closest('input')[0]
  jqInputgroup.closest('.btn-secondary').removeClass('btn-secondary')

  if (jqEl.hasClass('jsDate')) {
    input.setAttribute('placeholder', '')
    input.setAttribute('type', 'date')
  }

  if (jqEl.hasClass('jsInterval')) {
    input.setAttribute('placeholder', '12M')
    input.setAttribute('type', 'text')
  }

  jqEl.addClass('btn-secondary')
})

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
    return $("a[href='"+ window.location.hash + "']").click()
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

      // dont save logging tab
      if (href === '#logging') {
        return
      }

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

/**
 * Init Select2 fields and resize them. Also get data via AJAX for specific
 * fields.
 */
var initSelect2Fields = function() {
  $(window).resize(function() {
    $('.select2').css('width', "100%");
  });

  let lang = document.documentElement.lang

  if ($('.select2-ajax').length) {
    $(".select2-ajax").each(function () {
      let searchTerm = ''
      let full = false

      // credit to https://github.com/select2/select2/issues/3902#issuecomment-708772383
      $(this).on('select2:closing', function (e) {
        // Preserve typed value
        searchTerm = $('.select2-search input').prop('value')
      }).on('select2:open', function (e) {
          // Fill preserved value back into Select2 input field and trigger the AJAX loading (if any)
          let search = $('.select2-search input').val(searchTerm).trigger('change')

          if (searchTerm) {
            search.trigger('input');
          }

          if (full) {
            $('.loading-results').hide()
          }
      }).on('select2:select', function (e) {
        $('.select2-search input').val('').trigger('change')
      });

      $(this).select2({
        language: lang,
        search: '',
        placeholder: $(this).find('option[value=""]').text(),
        ajax: {
          url: $(this).attr('ajax-route'),
          type: 'get',
          dataType: 'json',
          delay: 500,
          transport: function (params, success, failure) {
            if ((params.data.search === searchTerm || params.data.search === undefined) && full) {
              return false
            }

            full = false

            let $request = $.ajax(params);
            $request.then(success);
            $request.fail(failure);

            return $request;
          },
          data: function (params) {
            return {
              search: params.term, // search term
              page: params.page || 1
            }
          },
          processResults: function (response) {
            for (element in response.data) {
              if (response.data[element].count) {
                response.data[element].text += ' (' + response.data[element].count + ')'
              }
            }

            if (response.current_page == response.last_page) {
              full = true
            }

            return {
              results: response.data,
              pagination: {
                more: !! response.next_page_url
              }
            };
          },
          cache: true
        }
      })
    })
  }

  $("select").not('.select2-ajax').select2({language: lang});
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
      $(this).css({
          "height": this.scrollHeight + 5 + "px",
          "max-height": "85vh"
      });
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

function debounce(func, wait, immediate) {
  let timeout
  return function() {
      let context = this, args = arguments
      clearTimeout(timeout)
      timeout = setTimeout(function() {
          timeout = null
          if (!immediate) func.apply(context, args);
      }, wait)
      if (immediate && !timeout) func.apply(context, args);
  }
}

function redirect(i18nAll)
{
    let select = document.getElementById('show-value')
    let text = select.options[select.selectedIndex].text
    let url = window.location.href

    if (text == i18nAll) {
        return window.location.href = url.search(/model=/) == -1 ? url : url.replace(/\??&?model=\w+[^?&]+/, '')
    }

    if (url.search(/model=/) == -1) {
      let concat = url.search(/[?]/) != -1 ? '&' :'?'

      return window.location.href = url + concat + 'model=' + text
    }

    window.location.href = url.replace(/model=\w+[^?&]+/, 'model=' + text)
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

function getSearchTag()
{
  var element = document.getElementById('prefillSearchbar');
  document.getElementById('globalSearch').value = element.options[element.selectedIndex].value;
}

function linkTag()
{
  var element = document.getElementById('prefillSearchbar');
  var select = element.options[element.selectedIndex];
  var search = document.getElementById('globalSearch');

  // if you search 'ip:...' in 'all', still use the 'ip:' tag
  // if there is no tag, you search in 'all'
  Array.from(element.options).forEach(function(option) {
    if (search.value.startsWith(option.value) && option.value != '') {
      var querySelector = `[value='${option.value}']`;
      document.getElementById('globalSearchForm').action = document.querySelectorAll(querySelector)[0].dataset.route;
    } else {
      document.getElementById('globalSearchForm').action = select.dataset.route;
    }
  });
}

var NMS = function () {
	"use strict";

	return {
		//main function
		init: function () {
			makeNavbarSearch();
			initSelect2Fields();
			saveTabPillState();
      positionErdPopover();
      rezizeTextareas();
      rangeSlider();
		},
  };
}();
