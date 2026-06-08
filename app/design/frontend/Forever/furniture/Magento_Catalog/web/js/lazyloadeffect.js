require([
    'jquery',
    "jquery/ui",
    "lazyLoad"
], function($){
    'use strict';
    $(document).ready(function(){
        $('.lazy').lazy({
          effect: "fadeIn",
          effectTime: 2000,
          threshold: 0
        });
    });
});