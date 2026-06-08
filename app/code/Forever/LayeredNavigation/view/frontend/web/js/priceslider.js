define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Forever_LayeredNavigation/js/layerednav',
    'jquery-ui-modules/slider'
], function ($, util) {
    'use strict';

    $.widget('forever.LayerednavSlider', $.forever.layerednav, {
        options: {
            sliderElement: '#layerednav_price_slider',
            textElement: '#layered_ajax_price_text'
        },

        _create: function () {
            var self = this,
                initialized = false;

            $(this.options.sliderElement).slider({
                range: true,
                min: Number(self.options.minValue),
                max: Number(self.options.maxValue),
                values: [Number(self.options.selectedFrom), Number(self.options.selectedTo)],
                slide: function (event, ui) {
                    self.displayText(ui.values[0], ui.values[1]);
                },
                change: function (event, ui) {
                    if (initialized) {
                        self.ajaxSubmit(self.getUrl(ui.values[0], ui.values[1]));
                    }
                }
            });

            initialized = true;
            this.displayText(this.options.selectedFrom, this.options.selectedTo);
        },

        getUrl: function (from, to) {
            return this.options.ajaxUrl
                .replace(encodeURI('{price_start}'), from)
                .replace(encodeURI('{price_end}'), to);
        },

        displayText: function (from, to) {
            $(this.options.textElement).html(
                '<span class="from_fixed">' + this.formatPrice(from) + '</span>' +
                '<span class="space_fixed"> - </span>' +
                '<span class="to_fixed">' + this.formatPrice(to) + '</span>'
            );
        },

        formatPrice: function (value) {
            return util.formatPrice(value, this.options.priceFormat);
        }
    });

    return $.forever.LayerednavSlider;
});
