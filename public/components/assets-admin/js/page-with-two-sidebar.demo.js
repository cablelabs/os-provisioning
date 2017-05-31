/*   
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 2.1.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.1/admin/material/
*/

var getRandomValue = function() {
    var value = [];
    for (var i = 0; i<= 19; i++) {
        value.push(Math.floor((Math.random() * 10) + 1));
    }
    return value;
};

var handleRenderKnobDonutChart = function() {
    $('.knob').knob();
};

var handleRenderSparkline = function() {
    var red		    = '#F44336',
        pink		= '#E91E63',
        orange	    = '#FF9800',
        yellow       = '#FFEB3B';
        
    var options = {
        height: '50px',
        width: '100%',
        fillColor: 'transparent',
        type: 'bar',
        barWidth: 9.9,
        barColor: red
    };
    
    var value = getRandomValue();
    $('#sidebar-sparkline-1').sparkline(value, options);
    
    value = getRandomValue();
    options.barColor = pink;
    $('#sidebar-sparkline-2').sparkline(value, options);
    
    value = getRandomValue();
    options.barColor = orange;
    $('#sidebar-sparkline-3').sparkline(value, options);
    
    value = getRandomValue();
    options.barColor = yellow;
    $('#sidebar-sparkline-4').sparkline(value, options);
};

var PageWithTwoSidebar = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleRenderKnobDonutChart();
            handleRenderSparkline();
        }
    };
}();