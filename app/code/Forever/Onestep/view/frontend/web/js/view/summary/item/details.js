define([
    'jquery',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Ui/js/modal/confirm'
    ], function (
        $,
        checkoutData,
        quote,
        urlBuilder,
        storage,
        customerData,
        errorProcessor,
        fullScreenLoader,
        defaultTotal,
        cartCache,
        confirm
    ) {
    var item_parent;
    'use strict';
        var mixin = {

            defaults: {
                template: 'Forever_Onestep/summary/item/details',
            },
            initialize: function () {
                item_parent = this._super();
                return item_parent;
            },
            plusQty: function (element, data) {
                var input = $(data.target).parent().find('input');
                var qty = parseInt(input.val())+1;
                if(!qty){
                    qty = 1;
                }
                input.val(qty).trigger('change');
            },
            minusQty: function (element, data) {
                var input = $(data.target).parent().find('input');
                var qty = parseInt(input.val())-1;
                if(qty){
                    input.val(qty).trigger('change');
                }
            },
            updateItem: function (parent, event) 
            {   
                fullScreenLoader.startLoader();
                // console.log(parent, event);
                var target = $(event.currentTarget);
                var qty = target.parents('div.details-qty').find('input').val();
                var url = urlBuilder.createUrl('/guest-carts/'+quote.getQuoteId()+'/items/'+parent.item_id, {});
                if(window.checkoutConfig.isCustomerLoggedIn){
                   var url = urlBuilder.createUrl('/carts/mine/items/'+parent.item_id, {});
                }
                var result = storage.put(
                    url,
                    JSON.stringify({
                         quoteId: quote.getQuoteId(),
                        "cartItem": {
                            "quoteId": quote.getQuoteId(),
                            "item_id" : parent.item_id,
                            "qty": qty
                        }
                    })
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                ).done(function(){
                    var sections = ['cart','checkout-data'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, true).done(function () {
                    var items = customerData.get('cart')().items;
                    var cartData = customerData.get('cart')();
                    console.log(cartData);
                    $('.opc-block-summary .items-in-cart .title span:first').html(cartData.summary_count);
                        $(items).each(function(k,v){
                            if(!window.checkoutConfig.imageData[v.item_id]){
                                window.checkoutConfig.imageData[v.item_id]=v.product_image;
                            }
                        });
                        cartCache.set('totals',null);
                        defaultTotal.estimateTotals();
                        fullScreenLoader.stopLoader();
                    });
                });
            },


            removeItem: function (parent) {
                var self=this;
                confirm({
                    title: $.mage.__('Do you want to remove this item from cart?'),
                    content: $.mage.__(''),
                    buttons: [{
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',
                        click: function (event) {
                            this.closeModal(event, true);
                        }
                    }, {
                        text: $.mage.__('OK'),
                        class: 'action primary action-accept',
                        click: function (event) {
                            self.confRemoveItem(parent);
                            this.closeModal(event, true);
                        }
                    }]
                });
            },

            confRemoveItem: function (parent) {
                var url = urlBuilder.createUrl('/guest-carts/'+quote.getQuoteId()+'/items/'+parent.item_id, {});
                if(window.checkoutConfig.isCustomerLoggedIn){
                   url = urlBuilder.createUrl('/carts/mine/items/'+parent.item_id, {});
                }
                var result = storage.delete(
                    url,
                    JSON.stringify({
                        cartId: quote.id,
                        itemId: parent.item_id
                    })
                ).fail(
                    function (response) {
                        errorProcessor.process(response);   
                        fullScreenLoader.stopLoader();
                    }
                ).done(function(){
                    cartCache.set('totals',null);
                    defaultTotal.estimateTotals();

                    var sections = ['cart','checkout-data'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, false).done(function () {
                        var items = customerData.get('cart')().items;
                        var cartData = customerData.get('cart')();
                        $('.opc-block-summary .items-in-cart .title span:first').html(cartData.summary_count);
                        if(!items.length){
                            location.reload();
                        }
                    });;
                });                
            },
        };

        return function (target) {
            return target.extend(mixin);
        };
});