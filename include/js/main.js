/**
 * Created by user1 on 11.02.16.
 */
$(document).ready(function(){

    if($(window).width() < 760){
        $('.news .container').addClass('carousel slide');
        $('.news .container .row').addClass('carousel-inner')
        $('.news .carousel-indicators').css('display', 'block');
        var Sign = $('.feedback').html();
        $('.feedback').remove();
        $('<div class="feedback">'+Sign+'</div>').insertAfter('footer .logo');
        rebuildMenu()
    }

    $('.menu-close').click(function(){
        $('header .menu').slideToggle(1000, function(){
            if($('header .menu').css('display') == 'none'){
                $('.menu-close img').attr('src', '/images/design/menu-toggle.png');
            }
            else{
                $('.menu-close img').attr('src', '/images/design/cross.png');
            }
        });

        $('.bg-wt').slideToggle(700);

        return false;
    })
    $(function(){
        $('.bg-wt').click(function(event) {
            if ($(event.target).closest(".menu").length) return;
            $('header .menu').slideToggle(1000);
            $('.bg-wt').slideToggle(700);
            $('.menu-close img').attr('src', '/images/design/menu-toggle.png');
            event.stopPropagation();
        });
    });


    $('.map-cover').click(function(){
        $(this).hide();
    })
    $(".carousel-inner").swipe( {
        //Generic swipe handler for all directions
        swipeLeft:function(event, direction, distance, duration, fingerCount) {
            $(this).parent().carousel('prev');
        },
        swipeRight: function() {
            $(this).parent().carousel('next');
        },
        //Default is 75px, set to 0 for demo so any distance triggers swipe
        threshold:0
    });


})



function rebuildMenu(){
    var i = 1;
    var items = $('header .menu .item').length;
    var it = $('header .menu .item');
    $('header .menu .item').each(function(){
        $(this).replaceWith('<div class="item">'+it.eq(items - i).html()+'</div>');
        i++;
    })
}
