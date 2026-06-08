define([
    'jquery',
    'jquery/ui'
    ], function($) {
        'use strict';
        return function(config) {

        var stickyenable = config.stickey_type_enable;
        var stickytype = config.stickey_type;
        if(stickyenable) {
            $(window).load(function(){
                if(stickytype == 'sticky-1') {
                    $('.page-wrapper').addClass('sticky-header');
                }
                else {
                    $('.page-wrapper').removeClass('sticky-header');
                }
            });
            $(window).scroll(function(){
                if(stickytype != 'sticky-1') {
                    if ($(window).scrollTop() >= 150) {
                        $('.page-wrapper').addClass('sticky-header2');
                    }
                    else {
                        $('.page-wrapper').removeClass('sticky-header2');
                    }
                }
            });
        }
    }
});