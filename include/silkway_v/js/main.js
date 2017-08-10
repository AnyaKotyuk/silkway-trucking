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
        $('<div class="feedback">'+Sign+'</div>').insertAfter('.footer .logo')
    }

    less();
    $('.menu-close').click(function(){
        $('.header .menu').slideToggle(1000);
        return false;
    })
})

