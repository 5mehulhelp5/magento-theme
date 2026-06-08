define(
    [
        'jquery',
        'mage/storage',
        'mage/apply/main'
    ],
    function ($, storage, mage) {
        'use strict';

        var layerContainer = $('.layered-filter-block-container'),
            quickViewContainer = $('#mpquickview-popup');

        return function (submitUrl, isChangeUrl) {
            /** save active state */
            var actives = [],
                data;
            $('.filter-options-item').each(function (index) {
                if ($(this).hasClass('active')) {
                    actives.push($(this).attr('attribute'));
                }
            });
            window.layerActiveTabs = actives;

            /** start loader */
            //loader.startLoader();

            /** change browser url */
            if (typeof window.history.pushState === 'function' && (typeof isChangeUrl === 'undefined')) {
                window.history.pushState({url: submitUrl}, '', submitUrl);
            }

            return storage.get(submitUrl).done(
                function (response) {
                    if (response.backUrl) {
                        window.location = response.backUrl;
                        return;
                    }
                    if (response.navigation) {
                        layerContainer.html(response.navigation);
                    }
                    if (response.products) {
                        $('#layer-product-list').replaceWith(response.products);
                    }
                    if (response.quickview) {
                        quickViewContainer.html(response.quickview);
                    }

                    if (mage) {
                        mage.apply();
                    }
                }
            ).fail(
                function () {
                    window.location.reload();
                }
            ).always(
                function () {

                    var colorAttr = $('.filter-options .filter-options-item .color .swatch-option-link-layered .swatch-option');
                    colorAttr.each(function () {
                        var el  = $(this),
                            hex = el.attr('data-option-tooltip-value');
                        if (typeof hex != "undefined") {
                            if (hex.charAt(0) === '#') {
                                hex = hex.substr(1);
                            }
                            if ((hex.length < 2) || (hex.length > 6)) {
                                el.attr('style','background: ' + el.attr('data-option-label') + ';');
                            }
                            var values = hex.split(''),
                                r,
                                g,
                                b;

                            if (hex.length === 2) {
                                r = parseInt(values[0].toString() + values[1].toString(), 16);
                                g = r;
                                b = r;
                            } else if (hex.length === 3) {
                                r = parseInt(values[0].toString() + values[0].toString(), 16);
                                g = parseInt(values[1].toString() + values[1].toString(), 16);
                                b = parseInt(values[2].toString() + values[2].toString(), 16);
                            } else if (hex.length === 6) {
                                r = parseInt(values[0].toString() + values[1].toString(), 16);
                                g = parseInt(values[2].toString() + values[3].toString(), 16);
                                b = parseInt(values[4].toString() + values[5].toString(), 16);
                            } else {
                                el.attr('style','background: ' + el.attr('data-option-label') + ';');
                            }

                            el.attr('style','background: center center no-repeat rgb(' + [r, g, b] + ');');
                        }

                    });

                    //selected

                    var filterCurrent = $('.layered-filter-block-container .filter-current .items .item .filter-value');

                    filterCurrent.each(function () {
                        var el         = $(this),
                            colorLabel = el.html(),
                            colorAttr  = $('.filter-options .filter-options-item .color .swatch-option-link-layered .swatch-option');

                        colorAttr.each(function () {
                            var elA = $(this);
                            if (elA.attr('data-option-label') === colorLabel && !elA.hasClass('selected')) {
                                elA.addClass('selected');
                            }
                        });
                    });

                    //loader.stopLoader();
                }
            );
        };
    }
);
