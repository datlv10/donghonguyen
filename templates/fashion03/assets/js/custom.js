var btnBackToTop = $('#back-totop');

if (btnBackToTop.length != 0) {
    $(window).scroll(function() {
        if ($(window).scrollTop() > 10) {
            btnBackToTop.addClass('show');
        } else {
            btnBackToTop.removeClass('show');
        }
    });
    
    btnBackToTop.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0}, '300');
    });
}