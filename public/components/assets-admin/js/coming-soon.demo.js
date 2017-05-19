/*   
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 2.1.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.1/admin/material/
*/

var handleCountdownTimer = function() {
    var newYear = new Date();
    newYear = new Date(newYear.getFullYear() + 1, 1 - 1, 1);
    $('#timer').countdown({until: newYear});
};

var ComingSoon = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleCountdownTimer();
        }
    };
}();