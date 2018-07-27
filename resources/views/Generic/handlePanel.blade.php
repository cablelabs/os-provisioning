<script language="javascript">
$(document).ready(function() {
    var handlePanel = function () {
    "use strict";
    var target = $('.panel').parent('[class*=col]');
    var targetHandle = '.panel-heading';
    var connectedTarget = '.row > [class*=col]';

    $(target).sortable({
        handle: targetHandle,
        connectWith: connectedTarget,
        stop: function (event, ui) {
            ui.item.find('.panel-title').append('<i class="fa fa-refresh fa-spin m-l-5" data-id="title-spinner"></i>');
            handlePanelPosition(ui.item);
        }
    });
    };

    var handlePanelPosition = function(element) {
        "use strict";
        if ($('.ui-sortable').length !== 0) {
        var newValue = [];
        var index = 0;
        $.when($('.ui-sortable').each(function() {
            var panelSortableElement = $(this).find('[data-sortable-id]');
            if (panelSortableElement.length !== 0) {
                var columnValue = [];
                $(panelSortableElement).each(function() {
                    var targetSortId = $(this).attr('data-sortable-id');
                    columnValue.push({id: targetSortId});
                });
                newValue.push(columnValue);
            } else {
                newValue.push([]);
            }
            index++;
        })).done(function() {
            var targetPage = "{!! isset($view_header) ? $view_header : ''!!}";
            localStorage.setItem(targetPage, JSON.stringify(newValue));
            $(element).find('[data-id="title-spinner"]').delay(500).fadeOut(500, function() {
                $(this).remove();
            });
        });
    }
    };

    var handlePanelStorage = function() {
        "use strict";
        if (typeof(Storage) !== 'undefined' && typeof(localStorage) !== 'undefined') {
            var targetPage = "{!! isset($view_header) ? $view_header : ''!!}";
            var panelPositionData = localStorage.getItem(targetPage);

            if (panelPositionData) {
                panelPositionData = JSON.parse(panelPositionData);
                var i = 0;
                $.when($('.panel').parent('[class*="col-"]').each(function() {
                    var storageData = panelPositionData[i];
                    var targetColumn = $(this);
                    if (storageData) {
                        $.each(storageData, function(index, data) {
                            var targetId = $('[data-sortable-id="'+ data.id +'"]').not('[data-init="true"]');
                            if ($(targetId).length !== 0) {
                                var targetHtml = $(targetId).clone();
                                $(targetId).remove();
                                $(targetColumn).append(targetHtml);
                                $('[data-sortable-id="'+ data.id +'"]').attr('data-init','true');
                            }
                        });
                    }
                    i++;
                })).done(function() {
                    window.dispatchEvent(new CustomEvent('localstorage-position-loaded'));
                });
            }
        } else {
            alert('Your browser is not supported with the local storage');
        }
    };

    handlePanel();
    handlePanelStorage();
});
</script>
