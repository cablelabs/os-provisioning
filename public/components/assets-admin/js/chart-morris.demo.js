/*   
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 2.1.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.1/admin/material/
*/

var blue		= '#2196F3',
    blueLight	= '#64B5F6',
    blueDark	= '#1976D2',
    aqua		= '#03A9F4',
    aquaLight	= '#4FC3F7',
    aquaDark	= '#0288D1',
    green		= '#009688',
    greenLight	= '#4DB6AC',
    greenDark	= '#00796B',
    orange		= '#FF9800',
    orangeLight	= '#FFB74D',
    orangeDark	= '#F57C00',
    dark		= '#212121',
    grey		= '#9E9E9E',
    purple		= '#673AB7',
    purpleLight	= '#9575CD',
    purpleDark	= '#512DA8',
    orange      = '#FF9800',
    pink        = '#E91E63',
    red         = '#F44336';
    
var handleMorrisLineChart = function () {
    var tax_data = [
        {"period": "2011 Q3", "licensed": 3407, "sorned": 660},
        {"period": "2011 Q2", "licensed": 3351, "sorned": 629},
        {"period": "2011 Q1", "licensed": 3269, "sorned": 618},
        {"period": "2010 Q4", "licensed": 3246, "sorned": 661},
        {"period": "2009 Q4", "licensed": 3171, "sorned": 676},
        {"period": "2008 Q4", "licensed": 3155, "sorned": 681},
        {"period": "2007 Q4", "licensed": 3226, "sorned": 620},
        {"period": "2006 Q4", "licensed": 3245, "sorned": null},
        {"period": "2005 Q4", "licensed": 3289, "sorned": null}
    ];
    Morris.Line({
        element: 'morris-line-chart',
        data: tax_data,
        xkey: 'period',
        ykeys: ['licensed', 'sorned'],
        labels: ['Licensed', 'Off the road'],
        resize: true,
        lineColors: [dark, blue]
    });
};
    
var handleMorrisBarChart = function () {
    Morris.Bar({
        element: 'morris-bar-chart',
        data: [
            {device: 'iPhone', geekbench: 136},
            {device: 'iPhone 3G', geekbench: 137},
            {device: 'iPhone 3GS', geekbench: 275},
            {device: 'iPhone 4', geekbench: 380},
            {device: 'iPhone 4S', geekbench: 655},
            {device: 'iPhone 5', geekbench: 1571}
        ],
        xkey: 'device',
        ykeys: ['geekbench'],
        labels: ['Geekbench'],
        barRatio: 0.4,
        xLabelAngle: 35,
        hideHover: 'auto',
        resize: true,
        barColors: [dark]
    });
};

var handleMorrisAreaChart = function() {
    Morris.Area({
        element: 'morris-area-chart',
        data: [
            {period: '2010 Q1', iphone: 2666, ipad: null, itouch: 2647},
            {period: '2010 Q2', iphone: 2778, ipad: 2294, itouch: 2441},
            {period: '2010 Q3', iphone: 4912, ipad: 1969, itouch: 2501},
            {period: '2010 Q4', iphone: 3767, ipad: 3597, itouch: 5689},
            {period: '2011 Q1', iphone: 6810, ipad: 1914, itouch: 2293},
            {period: '2011 Q2', iphone: 5670, ipad: 4293, itouch: 1881},
            {period: '2011 Q3', iphone: 4820, ipad: 3795, itouch: 1588},
            {period: '2011 Q4', iphone: 15073, ipad: 5967, itouch: 5175},
            {period: '2012 Q1', iphone: 10687, ipad: 4460, itouch: 2028},
            {period: '2012 Q2', iphone: 8432, ipad: 5713, itouch: 1791}
        ],
        xkey: 'period',
        ykeys: ['iphone', 'ipad', 'itouch'],
        labels: ['iPhone', 'iPad', 'iPod Touch'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true,
        lineColors: [red, orange, dark]
    });
};

var handleMorrisDonusChart = function() {
    Morris.Donut({
        element: 'morris-donut-chart',
        data: [
            {label: 'Jam', value: 25 },
            {label: 'Frosted', value: 40 },
            {label: 'Custard', value: 25 },
            {label: 'Sugar', value: 10 }
        ],
        formatter: function (y) { return y + "%" },
        resize: true,
        colors: [dark, orange, pink, grey]
    });
};


var MorrisChart = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleMorrisLineChart();
            handleMorrisBarChart();
            handleMorrisAreaChart();
            handleMorrisDonusChart();
        }
    };
}();