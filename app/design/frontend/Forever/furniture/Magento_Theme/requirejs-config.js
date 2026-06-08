var config = {
	paths:{
        'custom':"Magento_Theme/js/custom"
    },
    map: {
        '*': {
        'cartflip': 'Magento_Theme/js/cartflip',
        'productflip': 'Magento_Theme/js/product-flip',
        'carousel': 'Magento_Theme/js/carousel',
        'owlCarousel': 'Magento_Theme/js/owl-carousel/owl.carousel',
        'stickyheader': 'Magento_Theme/js/sticky'
      }
    },
    shim: {
        'custom': {
            "deps": ['jquery']
        },
        'cartflip': {
            "deps": ["jquery"]
        },
        'productflip': {
            "deps": ["cartflip"]
        },
        'owlCarousel': {
            "deps": ["jquery"]
        }
    }
}