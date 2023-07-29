$(document).ready(function(){
    // Show correct feature tab when enter page
    var currentUrl = window.location.href;
    features_nav_click(currentUrl);

    // Show correct feature tab when select on navigation
    $('.features_nav a').click(function (e) {
        features_nav_click($(this)[0].href);       
        $('nav').toggleClass('active');
        $('.hamburger').toggleClass('active');
    });            

    // Features selection expand
    $('.features-list').click(function (e) {
        $(this).toggleClass('expanded');
    });
    $('.features-selected').text($('#v-pills-tab > li > a.active').text());
    var newOptions = $('#v-pills-tab > li');
    newOptions.click(function() {
        $('.features-selected').text($(this).text());
        $('#v-pills-tab> li').removeClass('active');
        $(this).addClass('active');
        $("html, body").animate({
            scrollTop: $(".features").offset().top-150
        });
    });

    // Features selection sticky on scroll on mobile
    $(window).scroll(function() {                    
        var features_list_top = $('.features-list-title')[0].getBoundingClientRect().top;
        if (features_list_top <= 26){
            $('.features-list').addClass("sticky");
            $('header').addClass("no-shadow");
        }
        else{
            $('.features-list').removeClass("sticky");
            $('header').removeClass("no-shadow");
        }
    })

    // Back to top in tablet
    $('.back-top').click(function (e) {
        $("html, body").animate({
            scrollTop: $(".features").offset().top - 150
        });
    });   

})  

function features_nav_click(url){
    var tabParam = url.split('#')[1];
    if(tabParam !== undefined){
        $("html, body").animate({
            scrollTop: $(".features").offset().top - 150
        });
        $('#v-pills-tab a[href="#v-pills-'+tabParam+'"]').tab('show');   
        $('.features-selected').text($('#v-pills-tab > li > a.active').text());
    }    
}