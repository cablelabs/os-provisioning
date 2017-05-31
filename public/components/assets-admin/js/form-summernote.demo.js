/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 2.1.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.1/admin/angularjs2/
*/

var handleSummernote = function() {
    $('.summernote').summernote({
        placeholder: 'Hi, this is summernote. Please, write text here! Super simple WYSIWYG editor on Bootstrap',
        height: $(window).height() - $('.summernote').offset().top - 80
    });
};

var FormSummernote = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleSummernote();
        }
    };
}();