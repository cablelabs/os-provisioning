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

// Own JS NMS Prime File for our specific functions
$('.jsDateInterval').each(function () {
  $(this.parentNode).addClass('flex')
  $(this).addClass('h-100')
  $(this).after(`
    <div class="btn btn-secondary ml-2 jsToggle jsDate"><i class="fa mr-0 fa-calendar"></i></div>
    <div class="btn jsToggle jsInterval"><i class="fa mr-0 fa-hourglass-o"></i></div>
  `)
})

$(function () {
  $('.jsToggle').click(function (event) {
    let jqEl = $(this)
    if (jqEl.hasClass('btn-secondary')) {
      return
    }

    let jqInputgroup = jqEl.siblings()
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
})

/* This bit can be used on the entire app over all pages and will work for both tabs and pills.
 * Also, make sure the tabs or pills are not active by default,
 * otherwise you will see a flicker effect at page load.
 * Important: Make sure the parent ul has an id. Thanks Alain
 * http://stackoverflow.com/posts/16984739/revisions
 */
var saveTabPillState = function () {
  $(function () {
    var json, tabsState
    $('a[data-toggle="pill"], a[data-toggle="tab"]').on(
      'shown.bs.tab',
      function (e) {
        var href, json, parentId, tabsState

        tabsState = localStorage.getItem('tabs-state')
        json = JSON.parse(tabsState || '{}')
        parentId = $(e.target)
          .parents('ul.nav.nav-pills, ul.nav.nav-tabs, .nms-tabs')
          .attr('id')
        href = $(e.target).attr('href')
        json[parentId] = href

        // dont save logging tab
        if (href === '#logging') {
          return
        }

        return localStorage.setItem('tabs-state', JSON.stringify(json))
      }
    )

    tabsState = localStorage.getItem('tabs-state')
    json = JSON.parse(tabsState || '{}')

    $.each(json, function (containerId, href) {
      return $('#' + containerId + " a[href='" + href + "']").tab('show')
    })

    $('ul.nav.nav-pills, ul.nav.nav-tabs').each(function () {
      var $this = $(this)
      if (!json[$this.attr('id')]) {
        return $this
          .find('a[data-toggle=tab]:first, a[data-toggle=pill]:first')
          .tab('show')
      }
    })
  })
}

/**
 * Init Select2 fields and resize them. Also get data via AJAX for specific
 * fields.
 */
var initSelect2Fields = function () {
  $(window).resize(function () {
    $('.select2').css('width', '100%')
  })

  let lang = document.documentElement.lang

  if ($('.select2-ajax').length) {
    $('.select2-ajax').each(function () {
      window.initAjaxSelect2($(this), lang)
    })
  }

  window.initDefaultSelect2($('select').not('.select2-ajax,.nms-select2'))

  $('.select2').css('width', '100%')
}

window.initDefaultSelect2 = function (item, lang = null) {
  item.select2({ language: lang })
  .on('select2:open', function (e) {
    if (! e.target.multiple) {
      setTimeout(function() {document.querySelector('input.select2-search__field').focus();}, 300);
    }
  })
  .on('select2:select', function (e) {
    filterSelect2({e, item, lang})
  })
  .on('select2:unselect', function (e) {
    filterSelect2({e, item, lang})
  }).trigger('select2:select');
}

function filterSelect2(payload) {
  const {e, item, lang} = payload
  const selectedValue = e.target.value

  for(let i=0; i< item.length; i++) {
    if ($(item[i]).data('parent-id') == e.target.id) {
      $(item[i]).find('option').each(function() {
        const optionAttr = $(this).data('parent');

        if (selectedValue === optionAttr || selectedValue === '' || optionAttr === 'all') {
          $(this).attr('disabled', false);
        } else {
          $(this).attr('disabled', true);
        }
      });

      $(item[i]).select2({ language: lang })
    }
  }
}

window.initAjaxSelect2 = function (item, lang = null) {
  if(!lang) {
    lang = document.documentElement.lang
  }
  let searchTerm = ''
  let full = false

  // credit to https://github.com/select2/select2/issues/3902#issuecomment-708772383
  item
    .on('select2:closing', function (e) {
      // Preserve typed value
      searchTerm = $('.select2-search input').prop('value')
    })
    .on('select2:open', function (e) {
      // Fill preserved value back into Select2 input field and trigger the AJAX loading (if any)
      let search = $('.select2-search input')
        .val(searchTerm)
        .trigger('change')

      if (searchTerm) {
        search.trigger('input')
      }

      if (full) {
        $('.loading-results').hide()
      }

      setTimeout(function() {document.querySelector('input.select2-search__field').focus();}, 300);
    })
    .on('select2:select', function (e) {
      $('.select2-search input').val('').trigger('change')
    })

  item.select2({
    language: lang,
    search: '',
    placeholder: item.find('option[value=""]').text(),
    ajax: {
      url: item.attr('ajax-route'),
      type: 'get',
      dataType: 'json',
      delay: 500,
      transport: function (params, success, failure) {
        if (
          (params.data.search === searchTerm ||
            params.data.search === undefined) &&
          full
        ) {
          return false
        }

        full = false

        let $request = $.ajax(params)
        $request.then(success)
        $request.fail(failure)

        return $request
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
            response.data[element].text +=
              ' (' + response.data[element].count + ')'
          }
        }

        if (response.current_page == response.last_page) {
          full = true
        }

        setTimeout(function() {document.querySelector('input.select2-search__field').focus();}, 300);

        return {
          results: response.data,
          pagination: {
            more: !!response.next_page_url
          }
        }
      },
      cache: false
    }
  })
}

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
jQuery(function() {
  $('#selectall').on('click', function (e) {
    let checkboxes = $('#datatable').find('td input:checkbox:enabled')
    let isCheckbox = $(e.target).is('#allCheck')
    let currentState = $('#allCheck').prop('checked')
    currentState = isCheckbox ? ! currentState : currentState

    if (! isCheckbox) {
      $('#allCheck').prop('checked', !currentState)
    }
    checkboxes.prop('checked', !currentState)
  })

  $('.datatable, .clickableRow').on('click', function (e) {
    console.log(e.target)
    console.log($(e.target).is('input:checkbox'))
    console.log($(e.target).hasClass('index_check'))
    if (
      ($(e.target).is('input:checkbox') ||
      $(e.target).hasClass('index_check')) &&
      !$(e.target).find('input:checkbox').is(':disabled')
    ) {
      let checkbox = $(e.target).find('input:checkbox')

      if (! $(e.target).is('input:checkbox')) {
        checkbox.prop('checked', !checkbox.prop('checked'))
      }

      if ($('#allCheck').prop('checked')) {
        $('#allCheck').prop('checked', false)
      }

      if (
        $('#datatable').find('td input:checkbox:enabled').length ==
        $('#datatable').find('td input:checkbox:checked').length
      ) {
        $('#allCheck').prop('checked', true)
      }

      return
    }

    if ($(e.target).hasClass('ClickableTd') && $(e.target).is('td')) {
      window.location = $(e.target.parentNode).find('a').attr('href')
    }
  })
})

$('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
  $($.fn.dataTable.tables(true))
    .DataTable()
    .columns.adjust()
    .responsive.recalc()
})

function rezizeTextareas() {
  $('textarea')
    .each(function () {
      $(this).css({
        height: (this.scrollHeight < 48 ? 48 : this.scrollHeight + 5) + 'px',
        'max-height': '85vh',
        'margin-bottom': '3px',
      })
    })
    .on('input', function () {
      var scrollLeft =
        window.pageXOffset ||
        (document.documentElement || document.body.parentNode || document.body)
          .scrollLeft
      var scrollTop =
        window.pageYOffset ||
        (document.documentElement || document.body.parentNode || document.body)
          .scrollTop
      this.style.height = 'auto'
      this.style.height = this.scrollHeight + 10 + 'px'
      window.scrollTo(scrollLeft, scrollTop)
    })
}

window.debounce = function(func, wait, immediate) {
  let timeout
  return function () {
    let context = this,
      args = arguments
    clearTimeout(timeout)
    timeout = setTimeout(function () {
      timeout = null
      if (!immediate) func.apply(context, args)
    }, wait)
    if (immediate && !timeout) func.apply(context, args)
  }
}

function redirect(i18nAll) {
  let select = document.getElementById('show-value')
  let text = select.options[select.selectedIndex].text
  let url = window.location.href

  if (text == i18nAll) {
    return (window.location.href =
      url.search(/model=/) == -1
        ? url
        : url.replace(/\??&?model=\w+[^?&]+/, ''))
  }

  if (url.search(/model=/) == -1) {
    let concat = url.search(/[?]/) != -1 ? '&' : '?'

    return (window.location.href = url + concat + 'model=' + text)
  }

  window.location.href = url.replace(/model=\w+[^?&]+/, 'model=' + text)
}

/*
 * Init form range slider
 */
function rangeSlider(argument) {
  $('#slider').ionRangeSlider({
    type: 'single',
    grid: true
  })
}

document.addEventListener('DOMContentLoaded', function () {
  const datatable_selector = document.querySelector(".datatable")
  if (! datatable_selector) {
    return
  }

  document.querySelectorAll('[data-table]').forEach(function (el) {
    let id = el.getAttribute('id')
    $(`#${id}`).DataTable({
      dom: 'rt' + "<'row'<'col-12 pb-1'i><'col-9 ml-auto'p>>",
      columnDefs: [
        {
          defaultContent: '',
          targets: '_all'
        }
      ],
      processing: true,
      serverSide: true,
      deferRender: true,
      columns: [
        { data: 'checkbox', orderable: false, searchable: false },
        { data: 'label', orderable: false, searchable: false }
      ],
    })
  })

  // resize observer for resize_datatable
  let timer_resize_datatable = null

  const resize_datatable = new ResizeObserver(function(entries) {
    clearTimeout(timer_resize_datatable);
    timer_resize_datatable = setTimeout(() => {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust()
    }, 500)
  });

  resize_datatable.observe(datatable_selector)
})

window.excerptStr = function (str, length = 10) {
  return str.length > length ? `${str.substring(0, length)}...` : str;
}

window.NMS = (function () {
  'use strict'

  return {
    //main function
    init: function () {
      initSelect2Fields()
      saveTabPillState()
      rezizeTextareas()
      rangeSlider()
    },
    isEmpty: function (value) {
      if (Array.isArray(value) || typeof value === 'string') {
        return value.length === 0;
      } else if (typeof value === 'object' && value !== null) {
        return Object.keys(value).length === 0;
      } else {
        return true;
      }
    }
  }
})()
