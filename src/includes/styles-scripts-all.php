<?php
add_action( 'wp_enqueue_scripts', function() {
    $themeUrl = get_template_directory_uri();

    // css files
    wp_enqueue_style( 'scrollable-css', $themeUrl . '/assets/vender/scrollable/scrollable.css?ver=1.0.1' );
    wp_enqueue_style( 'aos-css', $themeUrl . '/assets/vender/aos/aos.css?ver=1.0.1' );
    wp_enqueue_style( 'style-css', $themeUrl . '/assets/css/common.css?ver=1.0.1' );

    if(is_front_page()){
        wp_enqueue_style( 'slick-css', $themeUrl . '/assets/vender/slick/slick.css?ver=1.0.1' );
        wp_enqueue_style( 'top-css', $themeUrl . '/assets/css/top.css?ver=1.0.1' );
    }

    if(is_page('reason')){
        wp_enqueue_style( 'slick-css', $themeUrl . '/assets/vender/slick/slick.css?ver=1.0.1' );
        wp_enqueue_style( 'reason-css', $themeUrl . '/assets/css/reason.css?ver=1.0.1' );
    }

    if(is_page('business')){
        wp_enqueue_style( 'slick-css', $themeUrl . '/assets/vender/slick/slick.css?ver=1.0.1' );
        wp_enqueue_style( 'business-css', $themeUrl . '/assets/css/business.css?ver=1.0.1' );
    }

    if(is_page('company')){
        wp_enqueue_style( 'slick-css', $themeUrl . '/assets/vender/slick/slick.css?ver=1.0.1' );
        wp_enqueue_style( 'company-css', $themeUrl . '/assets/css/company.css?ver=1.0.1' );
    }

    if(is_page('privacy')){
        wp_enqueue_style( 'privacy-css', $themeUrl . '/assets/css/privacy.css?ver=1.0.1' );
    }

    if(is_page('recruit')){
        wp_enqueue_style( 'recruit-css', $themeUrl . '/assets/css/recruit.css?ver=1.0.1' );
    }

    if(is_page(['contact','contact-g'])){
        wp_enqueue_style( 'contact-css', $themeUrl . '/assets/css/contact.css?ver=1.0.1' );
    }

    if(is_post_type_archive('news') || is_tax('news-cate')){
        wp_enqueue_style( 'news-css', $themeUrl . '/assets/css/news.css?ver=1.0.1' );
    }

    if(is_post_type_archive('works') || is_tax('works-cate')){
        wp_enqueue_style( 'works-css', $themeUrl . '/assets/css/works.css?ver=1.0.1' );
    }

    // js files

    wp_enqueue_script( 'jquery-js', $themeUrl . '/assets/vender/jquery/jquery-3.5.1.min.js', array(), '1.0', true );    
    wp_enqueue_script( 'scrollable-js', $themeUrl . '/assets/vender/scrollable/scrollable.js', array(), '1.0', true );
    wp_enqueue_script( 'aos-js', $themeUrl . '/assets/vender/aos/aos.js', array(), '1.0', true );
    wp_enqueue_script( 'cookie-js', $themeUrl . '/assets/js/cookie.js', array(), '1.0', true );
    wp_enqueue_script( 'common-js', $themeUrl . '/assets/js/common.js', array(), '1.0', true );
    

    // js only page, post
    if(is_front_page()){
        wp_enqueue_script( 'slick-js', $themeUrl . '/assets/vender/slick/slick.min.js', array(), '1.0', true );
        wp_enqueue_script( 'top-js', $themeUrl . '/assets/js/top.js', array(), '1.0', true );
    }

    if(is_page('reason')){
        wp_enqueue_script( 'slick-js', $themeUrl . '/assets/vender/slick/slick.min.js', array(), '1.0', true );
        wp_enqueue_script( 'reason-js', $themeUrl . '/assets/js/reason.js', array(), '1.0', true );
    }

    if(is_page('business')){
        wp_enqueue_script( 'slick-js', $themeUrl . '/assets/vender/slick/slick.min.js', array(), '1.0', true );
        wp_enqueue_script( 'business-js', $themeUrl . '/assets/js/business.js', array(), '1.0', true );
    }
    if(is_page('company')){
        wp_enqueue_script( 'slick-js', $themeUrl . '/assets/vender/slick/slick.min.js', array(), '1.0', true );
        wp_enqueue_script( 'company-js', $themeUrl . '/assets/js/company.js', array(), '1.0', true );
    }
    if(is_page(['contact','contact-g','recruit'])){
        wp_enqueue_script( 'yubinbango-js', $themeUrl . '/assets/vender/yubinbango/yubinbango.js', array(), '1.0', true );
    }
    
} );             
?>