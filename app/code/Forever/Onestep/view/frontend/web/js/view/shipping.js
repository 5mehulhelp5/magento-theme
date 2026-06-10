define([
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote'
], function (checkoutData, quote) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Forever_Onestep/shipping'
            },

            initialize: function () {
                var self = this,
                    initialized = false;

                this._super();

                quote.shippingMethod.subscribe(function () {
                    if (!initialized && checkoutData.getSelectedShippingRate()) {
                        initialized = true;
                        window.setTimeout(function () {
                            if (typeof self.setShippingInformation === 'function') {
                                self.setShippingInformation();
                            }
                        }, 1000);
                    }
                });

                return this;
            },

            selectShippingMethod: function (shippingMethod) {
                this._super(shippingMethod);

                if (typeof this.setShippingInformation === 'function') {
                    this.setShippingInformation();
                }

                return true;
            }
        });
    };
});
