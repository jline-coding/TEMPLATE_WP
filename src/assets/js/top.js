(function () { 
  $('.c-slider').slick({
    arrows: false,
    dots: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    speed: 500,
    autoplay: true,
    autoplaySpeed: 5000,
  });
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

  $('.c-slider02,.c-slider03').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 0,
    speed: 8000,
    cssEase: 'linear',
    vertical: true,
    verticalSwiping: false,
    arrows: false,
    pauseOnHover: false,
    pauseOnFocus: false,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          vertical: false,
          verticalSwiping: false,
          slidesToShow: 2,
          slidesToScroll: 1,
        }
      }
    ]
  });

  // $('.c-slider03').slick({
  //   slidesToShow: 4,
  //   slidesToScroll: 1,
  //   infinite: true,

  //   autoplay: true,
  //   autoplaySpeed: 0,
  //   speed: 3000,
  //   cssEase: 'linear',

  //   vertical: true,
  //   verticalSwiping: false,
  //   rtl: true,

  //   arrows: false,
  //   pauseOnHover: false,
  //   pauseOnFocus: false,
  //   draggable: false,
  //   swipe: false,
  //   touchMove: false,

  //   responsive: [
  //     {
  //       breakpoint: 768,
  //       settings: {
  //         vertical: false,
  //         verticalSwiping: false,
  //         slidesToShow: 2,
  //         slidesToScroll: 1,
  //         rtl: true
  //       }
  //     }
  //   ]
  // });




  function updateArrowPosition() {
    const imgHeight = $('.c-slider01__img').outerHeight();

    if (!imgHeight) return;

    $('.c-slider01__next,.c-slider01__prev').css(
      'top',
      `${imgHeight/2.25}px`
    );
  }

  // Catch copy character fade-in animation (hero_catch style)
  function initCatchCopyAnimation() {
    if (!$('html').hasClass('is-loadding')) {
      $('.c-loading').css('display', 'none');
      return 0;
    }

    var $text = $('.c-loading__text');
    if (!$text.length) return 0;

    // ============================================
    // LOADING ANIMATION CONFIG - 全ての速度調整はここ
    // ============================================
    var config = {
      charDelay: 0.06,        // 各文字の遅延間隔 (秒)
      initialDelay: 0.3,      // 最初の文字が出るまでの遅延 (秒)
      charAnimation: 2,       // 文字アニメーション時間 (秒) ※SCSS hero_catch と合わせる
      iconAnimation: 2,       // アイコンアニメーション時間 (秒) ※SCSS hero_icon と合わせる
      contentFade: 1,         // コンテンツフェードアウト時間 (秒) ※SCSS __content transition と合わせる
      loadingFade: 1.5,       // ローディング画面フェードアウト (秒) ※SCSS .c-loading transition と合わせる
      triggerAt: 2.0,         // コンテンツフェード開始タイミング (0~1, 文字の何割目で開始)
    };
    // ============================================

    var html = $text.html();
    var parts = html.split(/(<br\s*\/?>)/gi);
    var wrappedHtml = '';

    parts.forEach(function (part) {
      if (part.match(/^<br\s*\/?>$/i)) {
        wrappedHtml += part;
      } else {
        for (var i = 0; i < part.length; i++) {
          if (part[i] === ' ') {
            wrappedHtml += ' ';
          } else {
            wrappedHtml += '<span class="c-loading__char">' + part[i] + '</span>';
          }
        }
      }
    });

    $text.html(wrappedHtml);

    $text.css('visibility', 'visible');

    var $chars = $text.find('.c-loading__char');

    $chars.each(function (index) {
      $(this).css('animation-delay', (config.initialDelay + index * config.charDelay).toFixed(2) + 's');
    });

    $chars.addClass('is-visible');

    // アイコンと文字の終了時間を計算
    var lastCharEnd = config.initialDelay + ($chars.length - 1) * config.charDelay + config.charAnimation;
    var iconEnd = config.iconAnimation;
    var allAnimationsEnd = Math.max(lastCharEnd, iconEnd);

    // triggerAt で指定した文字のアニメーション終了時間
    var triggerIndex = Math.floor($chars.length * config.triggerAt);
    var triggerCharEnd = config.initialDelay + triggerIndex * config.charDelay + config.charAnimation;

    // content fade の開始: triggerAt の文字が終了 or icon 終了 のうち遅い方
    var contentFadeStart = Math.max(triggerCharEnd, iconEnd);

    // setTimeout で正確なタイミング制御
    setTimeout(function () {
      var $content = $('.c-loading__content');
      
      $content.addClass('is-fadeout');
      $content.one('transitionend', function (e) {
        if (e.target !== this || e.originalEvent.propertyName !== 'opacity') return;
        $('html').removeClass('is-loadding');
        var $loading = $('.c-loading');
        $loading.addClass('is-fadeout');
        $loading.one('transitionend', function (e) {
          if (e.target !== this || e.originalEvent.propertyName !== 'opacity') return;
          $(this).css('display', 'none');
        });
      });
    }, contentFadeStart * 1000);
  }

  $(window).on("load", function () {
    updateArrowPosition();
    initCatchCopyAnimation();
  });

  let resizeTimer;
  $(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(updateArrowPosition, 100);
  });
})();