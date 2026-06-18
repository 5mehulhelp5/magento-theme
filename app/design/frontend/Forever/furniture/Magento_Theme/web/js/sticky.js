define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        var stickyEnable = parseInt(config.stickey_type_enable, 10);
        var stickyType   = config.stickey_type;

        if (!stickyEnable) {
            return;
        }

        var updateStickyHeader = function () {
            var scrollTop = $(this).scrollTop();

            if (stickyType === 'sticky-1') {
                if (scrollTop >= 100) {
                    $('body').addClass('sticky-header-active').removeClass('sticky-header2-active');
                } else {
                    $('body').removeClass('sticky-header-active');
                }
            } else if (stickyType === 'sticky-2') {
                if (scrollTop >= 100) {
                    $('body').addClass('sticky-header2-active').removeClass('sticky-header-active');
                } else {
                    $('body').removeClass('sticky-header2-active');
                }
            }
        };

        $(window).on('scroll.stickyheader resize.stickyheader', updateStickyHeader);
        updateStickyHeader.call(window);
    };
});
