var config = {
    'config': {
        'mixins': {
            'Magento_Checkout/js/view/shipping': {
                'Forever_Onestep/js/view/shipping': true
            },
            'Magento_Checkout/js/view/payment': {
                'Forever_Onestep/js/view/payment': true
            },
            'Magento_Checkout/js/view/summary/item/details': {
                'Forever_Onestep/js/view/summary/item/details': true
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Forever_Onestep/js/view/summary/cart-items-mixin': true
            },
        }
    }
};