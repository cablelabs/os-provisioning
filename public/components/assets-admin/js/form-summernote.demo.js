/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7 & Bootstrap 4.0.0-Alpha 6
Version: 3.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v3.0/admin/html/
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