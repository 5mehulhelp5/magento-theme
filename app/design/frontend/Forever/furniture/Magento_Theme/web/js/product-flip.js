require([
    'jquery',
    "jquery/ui",
    "cartflip"
], function($){
    'use strict';
        // Product Cart Flip Js*/
           $(document).on("click",".tocart",function() { 
            var $container = $('[data-block=minicart]'),  $img, $effImg, $parent, src, $destination, $panelContent;
                $destination = $('[data-block=minicart]').first();
                var form = $(this).parents('.product-item-actions');
                $panelContent = $('[data-block=minicart] .block-minicart');
                if (form.parents('.product-item').length) {
                    $parent = form.parents('.product-item').first();
                    $img = $parent.find('.product-item-photo img').first();
                } else {
                    $img = $('.fotorama__active img.fotorama__img');
                }
                if ($img.length) {
                    $effImg = $('<img style="display: none; position:absolute; z-index:100000"/>');
                    $('body').append($effImg);
                    src = $img.attr('src');
                    var width = $img.width(), height = $img.height();
                    var step01Css = {
                        top: (($img.offset().top > $(window).scrollTop()) ? $img.offset().top : ($(window).scrollTop() + 10)),
                        left: $img.offset().left,
                        width: width,
                        height: height
                    }
                    $effImg.attr('src', src).css(step01Css);
                    var flyImage = function () {
                        $effImg.show();
                        var newWidth = 0.1*width, newHeight = 0.1*height;
                        var step02Css = {
                            top: $destination.offset().top,
                            left: $destination.offset().left,
                            width: newWidth,
                            height: newHeight
                        }
                        $effImg.animate(step02Css, 1000, 'linear', function () {
                            $effImg.fadeOut(100, 'swing', function () {
                                $effImg.remove();
                            });
                        });
                    }
                    flyImage();
                }
            });
        // Product Cart Flip Js*/
});