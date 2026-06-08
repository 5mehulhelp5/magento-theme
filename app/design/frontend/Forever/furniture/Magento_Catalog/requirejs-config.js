var config = {
    map: {
        '*': {
        'lazyLoad': 'Forever_Lazyload/js/lazy.min',
        'lazyloadeffect': 'Magento_Catalog/js/lazyloadeffect'
      }
    },
    shim: {
        'lazyLoad': {
            "deps": ["jquery"]
        },
        'lazyloadeffect': {
            "deps": ["lazyLoad"]
        }
    }
}