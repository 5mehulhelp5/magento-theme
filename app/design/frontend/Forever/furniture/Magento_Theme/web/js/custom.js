require([
    'jquery',
    'owlCarousel',
    "jquery/ui"
], function(jQuery){
    'use strict';
	jQuery(document).ready(function(){
		jQuery('.all-categories-section__content__details').hide();
		jQuery(".all-categories-section__content__heading").click(function(){
		  jQuery(".all-categories-section__content__details").slideToggle();
		  jQuery(".all-categories-section__content__details").toggleClass('active');
		});	

	    jQuery(".brand-slider").owlCarousel({
            responsive:{
                0:{
                    items:1
                },
                480:{
                    items:2
                },
                768:{
                    items:3
                },
                992:{
                    items:4
                },
                1200:{
                    items:4
                }
            },
            autoplay:false,
            loop:true,
            nav : true, // Show next and prev buttons
            dots: false,
            autoplaySpeed : 500,
            navSpeed : 500,
            dotsSpeed : 500,
            autoplayHoverPause: true,
        });

        jQuery(".testimonial-one").owlCarousel({
            responsive:{
                0:{
                    items:1
                },
                480:{
                    items:2
                },
                768:{
                    items:2
                },
                992:{
                    items:3
                },
                1200:{
                    items:3
                }
            },
            autoplay:false,
            loop:true,
            nav : true, // Show next and prev buttons
            dots: false,
            autoplaySpeed : 500,
            navSpeed : 500,
            dotsSpeed : 500,
            autoplayHoverPause: true,
            items: 3
        });
        jQuery(".testimonial-two").owlCarousel({
            responsive:{
                0:{
                    items:1
                },
                480:{
                    items:2
                },
                768:{
                    items:2
                },
                992:{
                    items:1
                },
                1200:{
                    items:1
                }
            },
            autoplay:false,
            loop:true,
            nav : true, // Show next and prev buttons
            dots: false,
            autoplaySpeed : 500,
            navSpeed : 500,
            dotsSpeed : 500,
            autoplayHoverPause: true,
        });

        jQuery(".blog-items").owlCarousel({
            responsive:{
                0:{
                    items:1
                },
                480:{
                    items:2
                },
                768:{
                    items:2
                },
                992:{
                    items:2
                },
                1200:{
                    items:2
                }
            },
            autoplay:false,
            loop:true,
            nav : true, // Show next and prev buttons
            dots: false,
            autoplaySpeed : 500,
            navSpeed : 500,
            dotsSpeed : 500,
            autoplayHoverPause: true,
        });
	});
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > 600) {
            jQuery(".top-bottom-arrow").fadeIn();
        } else {
            jQuery(".top-bottom-arrow").fadeOut();
        }
    })

    jQuery(".top-bottom-arrow").click(function() {
        jQuery("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    })

    jQuery('.switchpage-control .control').click(function(){
        jQuery(this).toggleClass('page-two');
        jQuery('.sub-list:first-child').toggleClass('remove-class');
        jQuery('.sub-list:last-child').toggleClass('add-class');
        setTimeout(function(){
         jQuery('.sub-list').toggleClass('annimated');  
       },1000)
    });
//     jQuery(window).scroll(function() { 
//         if($(window).width() > 1024) {
//     jQuery(function(jQuery) {
//         var num_cols = 2,
//         container = jQuery('.navigation > ul'),
//         listItem = 'li',
//         listClass = 'sub-list';
//         container.each(function() {
//             var items_per_col = new Array(),
//             items = jQuery(this).find(listItem),
//             min_items_per_col = Math.floor(items.length / num_cols),
//             difference = items.length - (min_items_per_col * num_cols);
//             for (var i = 0; i < num_cols; i++) {
//                 if (i < difference) {
//                     items_per_col[i] = min_items_per_col + 1;
//                 } else {
//                     items_per_col[i] = min_items_per_col;
//                 }
//             }
//             for (var i = 0; i < num_cols; i++) {
//                 jQuery(this).append(jQuery('<ul ></ul>').addClass(listClass));
//                 for (var j = 0; j < items_per_col[i]; j++) {
//                     var pointer = 0;
//                     for (var k = 0; k < i; k++) {
//                         pointer += items_per_col[k];
//                     }
//                     jQuery(this).find('.' + listClass).last().append(items[j + pointer]);
//                 }
//             }
//         });
//     });
// }
//     });
    jQuery('.faq-answers').hide();
    jQuery('.faq-questions').click(function(e) {
        e.preventDefault();
      
        var $this = jQuery(this);
      
        if ($this.parent().hasClass('show')) {
            $this.parent().removeClass('show');
            $this.next().slideUp(350);
        } else {
            $this.parent().find('.faq-questions').removeClass('show');
            $this.parent().toggleClass('show');
            $this.next().slideToggle(350);
        }
    });

    jQuery(".footer-area h6").on("click", function(){
        if (window.innerWidth < 993) {
            jQuery(this).toggleClass("active");
            jQuery(this).next("ul").slideToggle();
        }
    });
    jQuery(".footer-middle-head").on("click", function(){
        if (window.innerWidth < 768) {
            jQuery(this).toggleClass("active");
            jQuery(this).next("ul").slideToggle();
        }
    });
    jQuery(".footer-two .page-links h4").on("click", function(){
        if (window.innerWidth < 768) {
            jQuery(this).toggleClass("active");
            jQuery(this).next("ul").slideToggle();
        }
    });
    jQuery(".footer-three .footer-middle h4").on("click", function(){
        if (window.innerWidth < 768) {
            jQuery(this).toggleClass("active");
            jQuery(this).next("ul").slideToggle();
        }
    });
});

