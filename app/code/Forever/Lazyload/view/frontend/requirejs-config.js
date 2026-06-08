var config = {
    map: {
        "*": {
            lazyLoad: "Forever_Lazyload/js/lazy.min",
            lazyLoadPlugins: 'Forever_Lazyload/js/lazy.plugins.min',
            'fotorama/fotorama': 'Forever_Lazyload/js/fotorama'
        }
    },
    shim: {
        'lazyLoad': {
            'deps': ['jquery']
        },
        'lazyLoadPlugins': {
            'deps': ['jquery', 'lazyLoad']
        }
    }
};