define(['Magento_Checkout/js/checkout-data','Magento_Checkout/js/model/quote'], function (checkoutData, quote) {
    'use strict';
    var parent;
    var first;
    var mixin = {

        defaults: {
            template: 'Forever_Onestep/shipping',
        },
        initialize: function () {
            parent = this._super();
            quote.shippingMethod.subscribe(function () {
                if(!first && checkoutData.getSelectedShippingRate()){
                    setTimeout(function(){
                        parent.setShippingInformation();
                        first = true;
                    },1000);
                }
            });
            return parent;
        },
        selectShippingMethod: function (shippingMethod) {
            this._super();
            parent.setShippingInformation();
            return true;
        },
    };

    return function (target) {
        return target.extend(mixin);
    };

});
