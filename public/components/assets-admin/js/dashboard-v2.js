/*   
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 2.1.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.1/admin/material/
*/

var getMonthName = function(number) {
    var month = [];
    month[0] = "January";
    month[1] = "February";
    month[2] = "March";
    month[3] = "April";
    month[4] = "May";
    month[5] = "Jun";
    month[6] = "July";
    month[7] = "August";
    month[8] = "September";
    month[9] = "October";
    month[10] = "November";
    month[11] = "December";
    
    return month[number];
};

var getDate = function(date) {
    var currentDate = new Date(date);
    var dd = currentDate.getDate();
    var mm = currentDate.getMonth() + 1;
    var yyyy = currentDate.getFullYear();
    
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    currentDate = yyyy+'-'+mm+'-'+dd;
    
    return currentDate;
};

var handleVisitorsLineChart = function() {
    var green = '#009688';
    var greenLight = '#4DB6AC';
    var blue = '#2196F3';
    var blueLight = '#03A9F4';
    var blackTransparent = 'rgba(0,0,0,0.5)';
    var whiteTransparent = 'rgba(255,255,255,0.5)';
    
    Morris.Line({
        element: 'visitors-line-chart',
        data: [
            {x: '2014-02-01', y: 60, z: 30},
            {x: '2014-03-01', y: 70, z: 40},
            {x: '2014-04-01', y: 40, z: 10},
            {x: '2014-05-01', y: 100, z: 70},
            {x: '2014-06-01', y: 40, z: 10},
            {x: '2014-07-01', y: 80, z: 50},
            {x: '2014-08-01', y: 70, z: 40}
        ],
        xkey: 'x',
        ykeys: ['y', 'z'],
        xLabelFormat: function(x) {
            x = getMonthName(x.getMonth());
            return x.toString();
        },
        labels: ['Page Views', 'Unique Visitors'],
        lineColors: [green, blue],
        pointFillColors: [greenLight, blueLight],
        lineWidth: '2px',
        pointStrokeColors: [blackTransparent, blackTransparent],
        resize: true,
        gridTextFamily: 'Roboto',
        gridTextColor: whiteTransparent,
        gridTextWeight: 'normal',
        gridTextSize: '11px',
        gridLineColor: 'rgba(0,0,0,0.5)',
        hideHover: 'auto',
    });
};

var handleVisitorsDonutChart = function() {
    var green = '#009688';
    var blue = '#2196F3';
    Morris.Donut({
        element: 'visitors-donut-chart',
        data: [
            {label: "New Visitors", value: 900},
            {label: "Return Visitors", value: 1200}
        ],
        colors: [green, blue],
        labelFamily: 'Roboto',
        labelColor: 'rgba(255,255,255,0.5)',
        labelTextSize: '11px',
        backgroundColor: '#000'
    });
};

var handleVisitorsVectorMap = function() {
    if ($('#visitors-map').length !== 0) {
        $('#visitors-map').vectorMap({
            map: 'world_merc_en',
            scaleColors: ['#616161', '#616161'],
            container: $('#visitors-map'),
            normalizeFunction: 'linear',
            hoverOpacity: 0.5,
            hoverColor: false,
            markerStyle: {
                initial: {
                    fill: '#616161',
                    stroke: 'transparent',
                    r: 3
                }
            },
            regions: [{
                attribute: 'fill'
            }],
            regionStyle: {
                initial: {
                    fill: '#616161',
                    "fill-opacity": 1,
                    stroke: 'none',
                    "stroke-width": 0.4,
                    "stroke-opacity": 1
                },
                hover: {
                    "fill-opacity": 0.8
                },
                selected: {
                    fill: 'yellow'
                },
                selectedHover: {
                }
            },
            series: {
                regions: [{
                values: {
                    IN:'#E91E63',
                    US:'#E91E63',
                    KR:'#E91E63'
                }
                }]
            },
            focusOn: {
                x: 0.5,
                y: 0.5,
                scale: 2
            },
            backgroundColor: '#212121'
        });
    }
};

var handleScheduleCalendar = function() {
    var monthNames = ["January", "February", "March", "April", "May", "June",  "July", "August", "September", "October", "November", "December"];
    var dayNames = ["S", "M", "T", "W", "T", "F", "S"];

    var now = new Date(),
        month = now.getMonth() + 1,
        year = now.getFullYear();
        
    var events = [
        [
            '2/' + month + '/' + year,
            'Popover Title',
            '#',
            '#009688',
            'Some contents here'
        ],
        [
            '5/' + month + '/' + year,
            'Tooltip with link',
            'http://www.seantheme.com/color-admin-v1.3',
            '#212121'
        ],
        [
            '18/' + month + '/' + year,
            'Popover with HTML Content',
            '#',
            '#212121',
            'Some contents here <div class="text-right"><a href="http://www.google.com">view more >>></a></div>'
        ],
        [
            '28/' + month + '/' + year,
            'Color Admin V1.3 Launched',
            'http://www.seantheme.com/color-admin-v1.3',
            '#212121',
        ]
    ];
    var calendarTarget = $('#schedule-calendar');
    $(calendarTarget).calendar({
        months: monthNames,
        days: dayNames,
        events: events,
        popover_options:{
            placement: 'top',
            html: true
        }
    });
    $(calendarTarget).find('td.event').each(function() {
        var backgroundColor = $(this).css('background-color');
        $(this).removeAttr('style');
        $(this).find('a').css('background-color', backgroundColor);
    });
    $(calendarTarget).find('.icon-arrow-left, .icon-arrow-right').parent().on('click', function() {
        $(calendarTarget).find('td.event').each(function() {
            var backgroundColor = $(this).css('background-color');
            $(this).removeAttr('style');
            $(this).find('a').css('background-color', backgroundColor);
        });
    });
};

var handleDashboardGritterNotification = function() {
    $(window).load(function() {
        setTimeout(function() {
            $.gritter.add({
                title: 'Welcome back, Admin!',
                text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed tempus lacus ut lectus rutrum placerat.',
                image: 'assets/img/user.jpg',
                sticky: true,
                time: '',
                class_name: 'my-sticky-class'
            });
        }, 1000);
    });
};

var DashboardV2 = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleVisitorsLineChart();
            handleVisitorsDonutChart();
            handleVisitorsVectorMap();
            handleScheduleCalendar();
            handleDashboardGritterNotification();
        }
    };
}();