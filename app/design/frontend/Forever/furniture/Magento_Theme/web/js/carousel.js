define([
    'jquery',
    'jquery/ui',
    'owlCarousel'
], function($) {
    return function(config, element) {
        $(element).owlCarousel(config);
    };
});