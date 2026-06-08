
var config = {
	map: {
        '*': {
            infinitescroll: 'Forever_InfiniteScroll/js/plugin/infinitescroll',
        }
    },
	paths: {
		'forever/infinitescroll': 'Forever_InfiniteScroll/js/plugin/infinitescroll',
	},
	shim: {
		'forever/infinitescroll': {
			deps: ['jquery']
		}
	}

};