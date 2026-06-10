define([
    'jquery',
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
    'use strict';

    function getQuoteId() {
        return typeof quote.getQuoteId === 'function' ? quote.getQuoteId() : quote.id;
    }

    function getCartItemUrl(itemId) {
        var quoteId = getQuoteId();

        if (window.checkoutConfig && window.checkoutConfig.isCustomerLoggedIn) {
            return urlBuilder.createUrl('/carts/mine/items/' + itemId, {});
        }

        return urlBuilder.createUrl('/guest-carts/' + quoteId + '/items/' + itemId, {});
    }

    function refreshCheckoutData(forceReload) {
        var sections = ['cart', 'checkout-data'];

        customerData.invalidate(sections);

        return customerData.reload(sections, forceReload).done(function () {
            var cartData = customerData.get('cart')() || {},
                items = cartData.items || [];

            $('.opc-block-summary .items-in-cart .title span:first').html(cartData.summary_count || 0);

            if (window.checkoutConfig) {
                window.checkoutConfig.imageData = window.checkoutConfig.imageData || {};

                $.each(items, function (index, item) {
                    if (item && item.item_id && item.product_image && !window.checkoutConfig.imageData[item.item_id]) {
                        window.checkoutConfig.imageData[item.item_id] = item.product_image;
                    }
                });
            }
        });
    }

    function normalizeQty(value) {
        var qty = parseInt(value, 10);

        return qty > 0 ? qty : 1;
    }

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Forever_Onestep/summary/item/details'
            },

            getItemOptions: function (item) {
                var options = item && item.options ? item.options : '[]';

                try {
                    options = typeof options === 'string' ? JSON.parse(options) : options;
                } catch (error) {
                    options = [];
                }

                return Array.isArray(options) ? options : [];
            },

            plusQty: function (element, event) {
                var input = $(event.target).parent().find('input'),
                    qty = normalizeQty(input.val()) + 1;

                input.val(qty).trigger('change');
            },

            minusQty: function (element, event) {
                var input = $(event.target).parent().find('input'),
                    qty = normalizeQty(input.val()) - 1;

                if (qty > 0) {
                    input.val(qty).trigger('change');
                }
            },

            updateItem: function (parent, event) {
                var targetElement = $(event.currentTarget),
                    qty = normalizeQty(targetElement.parents('div.details-qty').find('input').val()),
                    quoteId = getQuoteId(),
                    url = getCartItemUrl(parent.item_id);

                fullScreenLoader.startLoader();

                storage.put(
                    url,
                    JSON.stringify({
                        quoteId: quoteId,
                        cartItem: {
                            quoteId: quoteId,
                            item_id: parent.item_id,
                            qty: qty
                        }
                    })
                ).fail(function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }).done(function () {
                    refreshCheckoutData(true).done(function () {
                        cartCache.set('totals', null);
                        defaultTotal.estimateTotals();
                        fullScreenLoader.stopLoader();
                    });
                });
            },

            removeItem: function (parent) {
                var self = this;

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
                var url = getCartItemUrl(parent.item_id);

                fullScreenLoader.startLoader();

                storage.delete(url).fail(function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }).done(function () {
                    cartCache.set('totals', null);
                    defaultTotal.estimateTotals();

                    refreshCheckoutData(false).done(function () {
                        var cartData = customerData.get('cart')() || {},
                            items = cartData.items || [];

                        fullScreenLoader.stopLoader();

                        if (!items.length) {
                            window.location.reload();
                        }
                    });
                });
            }
        });
    };
});
