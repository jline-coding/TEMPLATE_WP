let scroll_pos1 = 0;

// =============================
// Helper: Body lock/unlock (Modal)
// =============================
function addFixedBodyModal() {
    scroll_pos1 = $(window).scrollTop();
    $('body')
        .addClass('overflow_modal')
        .css({ top: -scroll_pos1 + 'px' });
}

function removeFixedBodyModal() {
    $('body').removeClass('overflow_modal').css({ top: '' });
    $(window).scrollTop(scroll_pos1);
}

// =============================
// Helper: Debounce
// =============================
function debounce(func, wait = 100) {
    let timeout;
    return function() {
        clearTimeout(timeout);
        timeout = setTimeout(func, wait);
    };
}

// =============================
// Scroll Behavior
// =============================
function handleScroll() {
    const scrollTop = $(window).scrollTop();

    // Header active & ToTop visibility
    if (scrollTop > 5) {
        $(".c-totop").css("transform", "translateY(0)");
        $(".c-header").addClass("active");
    } else {
        $(".c-totop").removeAttr("style");
        $(".c-header").removeClass("active");
    }
}

// =============================
// On Document Ready
// =============================
$(document).ready(function () {
    $('.js-morelink').on('mouseenter touchstart', function (e) {
        const id = $(this).data('id');
        $('.js-moreimg').removeClass('active');
        $('#' + id).addClass('active');
    });

    // Smooth anchor scroll
    $('a[href^="#"]').on('click', function (e) {
        const target = $($(this).attr("href"));
        if (target.length) {
            e.preventDefault();
            const offset = target.offset().top - ($('.c-toggle').outerHeight());
            $('html, body').animate({ scrollTop: offset }, 600);
        }
    });

    // Auto scroll to anchor if URL has hash
    const hash = location.hash;
    if (hash && $(hash).length) {
        const offset = $(hash).offset().top - ($('.c-toggle').outerHeight());
        $('html, body').animate({ scrollTop: offset }, 600);
    }

    // Menu toggle
    $(".c-toggle").on("click", function () {
        const isActive = $(this).hasClass("active");
        $(this).toggleClass("active")
            .find(".c-toggle__txt")
            .text(isActive ? "MENU" : "CLOSE");

        $(".c-gnavi").stop().slideToggle("fast");
        $(".c-header__btn").stop().fadeToggle("fast");
        isActive ? removeFixedBodyModal() : addFixedBodyModal();
    });

    // Initial scroll state
    handleScroll();
});

// =============================
// On Window Load
// =============================
$(window).on('load', function () {

    // Init AOS
    AOS.init({
        duration: 1000,
        once: true,
    });

    // Init ScrollHint
    if ($('.js_scrollable, .has-fixed-layout').length) {
        new ScrollHint('.js_scrollable, .has-fixed-layout', {
            scrollHintIconAppendClass: 'scroll-hint-icon-white',
            applyToParents: true,
            i18n: {
                scrollable: 'スクロールできます',
            },
        });
    }

    // header
    handleScroll();
});

// =============================
// On Scroll
// =============================

const debouncedHandleScroll = debounce(handleScroll, 50);

let latestScroll = 0;
let ticking = false;

$(window).on('scroll', function () {
  debouncedHandleScroll();
  latestScroll = window.scrollY || $(this).scrollTop();
  if (!ticking) {
    window.requestAnimationFrame(function () {
      const scrollTop = latestScroll;
      $('.c-header__logo').css({
        opacity: scrollTop < 10 ? 1 : 0,
        pointerEvents: scrollTop < 10 ? 'all' : 'none',
        transform: `translateY(${-scrollTop}px)`
      });
      ticking = false;
    });
    ticking = true;
  }
});

// =============================
// On Resize
// =============================
$(window).on('resize', debounce(function () {
    scroll_pos1 = $(window).scrollTop();

    if ($(window).width() > 767) {
        $(".c-gnavi").removeAttr("style");
        $(".c-toggle").removeClass("active");
        $('body').removeClass('overflow_modal').css({ top: '' });
        $(window).scrollTop(scroll_pos1);
    } else {
        $(".over").addClass("flag");
    }

    handleScroll();
}, 150));

function adjustSpacingInBlockeditor() {
  const container = document.querySelector('.is-layout-flow');
  if (!container) return;

  const elements = container.querySelectorAll('*');

  elements.forEach(el => {
    if (!el.dataset.originalSpacing) {
      el.dataset.originalSpacing = JSON.stringify({
        marginTop: el.style.marginTop || null,
        marginRight: el.style.marginRight || null,
        marginBottom: el.style.marginBottom || null,
        marginLeft: el.style.marginLeft || null,
        paddingTop: el.style.paddingTop || null,
        paddingRight: el.style.paddingRight || null,
        paddingBottom: el.style.paddingBottom || null,
        paddingLeft: el.style.paddingLeft || null,
        fontSize: el.style.fontSize || null,
      });
    }

    const original = JSON.parse(el.dataset.originalSpacing);

    if (window.innerWidth < 768) {
      [
        'marginTop','marginRight','marginBottom','marginLeft',
        'paddingTop','paddingRight','paddingBottom','paddingLeft'
      ].forEach(prop => {
        if (original[prop]) {
          const val = parseFloat(original[prop]);
          const unit = original[prop].replace(val, '');
          if (!isNaN(val)) {
            el.style[prop] = (val / 2) + unit;
          }
        }
      });

      if (original.fontSize) {
        const val = parseFloat(original.fontSize);
        const unit = original.fontSize.replace(val, '');
        if (!isNaN(val)) {
          el.style.fontSize = (val / 1.5) + unit;
        }
      }
    } else {
      Object.keys(original).forEach(prop => {
        if (original[prop]) {
          el.style[prop] = original[prop];
        }
      });
    }
  });
}

window.addEventListener('load', adjustSpacingInBlockeditor);
window.addEventListener('resize', adjustSpacingInBlockeditor);

$('.js-taps').each(function () {

    const $wrap = $(this);
    const $btns = $wrap.find('.js-tab');
    const $panels = $wrap.find('.js-tab-panel');

    $btns.on('click', function () {

      const targetId = $(this).data('id');
      const $target = $wrap.find('#' + targetId);

      if ($(this).hasClass('is-active')) return;

      $btns.removeClass('is-active');
      $(this).addClass('is-active');

      $panels.removeClass('is-active');
      $target.addClass('is-active');

    });

});