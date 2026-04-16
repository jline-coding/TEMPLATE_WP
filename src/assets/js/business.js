(function () { 
  $('.c-slider01').each(function () {

    var $slider = $(this);

    $slider.slick({
      arrows: true,
      dots: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      speed: 500,
      autoplay: true,
      autoplaySpeed: 5000,
      nextArrow: $slider.closest('.c-slider01-wrap').find('.c-slider01__next'),
      prevArrow: $slider.closest('.c-slider01-wrap').find('.c-slider01__prev'),
    });

  });
  

  function updateArrowPosition() {
    const imgHeight = $('.c-slider01__img').outerHeight();

    if (!imgHeight) return;

    $('.c-slider01__next,.c-slider01__prev').css(
      'top',
      `${imgHeight/2.25}px`
    );
  }

  $(window).on("load", function () {
    updateArrowPosition();
  });

  let resizeTimer;
  $(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(updateArrowPosition, 100);
  });
})();



