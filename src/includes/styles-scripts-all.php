<?php
add_action( 'wp_enqueue_scripts', function() {
    $themeUrl = get_template_directory_uri();

    $themeDir = get_template_directory();

    // css files
    wp_enqueue_style( 'scrollable-css', $themeUrl . '/assets/vendor/scrollable/scrollable.css', array(), '1.0.1' );
    wp_enqueue_style( 'mCustomScrollbar-css', $themeUrl . '/assets/vendor/mCustomScrollbar/jquery.mCustomScrollbar.css', array(), '1.0.1' );
    wp_enqueue_style( 'style-css', $themeUrl . '/assets/css/common.css', array(), filemtime($themeDir . '/assets/css/common.css') );

    if(is_front_page()){
        wp_enqueue_style( 'slick-css', $themeUrl . '/assets/vendor/slick/slick.css', array(), '1.0.1' );
        wp_enqueue_style( 'top-css', $themeUrl . '/assets/css/top.css', array(), filemtime($themeDir . '/assets/css/top.css') );
    }

    if(is_page(['mailformpro','contactform7'])){
        wp_enqueue_style( 'jquery-ui-css', $themeUrl . '/assets/vendor/jquery/jquery-ui.min.css', array(), '1.0.1' );
        wp_enqueue_style( 'contact-css', $themeUrl . '/assets/css/contact.css', array(), filemtime($themeDir . '/assets/css/contact.css') );
    }

    // js files — Replace WP's built-in jQuery with custom jQuery 4
    wp_deregister_script( 'jquery-core' );
    wp_deregister_script( 'jquery' );

    wp_register_script( 'jquery-core', $themeUrl . '/assets/vendor/jquery/jquery-4.0.0.min.js', array(), '4.0.0', array(
        'strategy' => 'defer',
    ));
    wp_register_script( 'jquery', false, array( 'jquery-core' ), '4.0.0', array(
        'strategy' => 'defer',
    ));
    wp_enqueue_script( 'jquery' );

    wp_enqueue_script( 'scrollable-js', $themeUrl . '/assets/vendor/scrollable/scrollable.js', array( 'jquery' ), '1.0', array(
        'strategy' => 'defer',
    ));
    wp_enqueue_script( 'mCustomScrollbar-js', $themeUrl . '/assets/vendor/mCustomScrollbar/jquery.mCustomScrollbar.js', array( 'jquery' ), '1.0', array(
        'strategy' => 'defer',
    ));
    
    wp_enqueue_script( 'inview-js', $themeUrl . '/assets/js/inview.js', array( 'jquery' ), '1.0', array(
        'strategy' => 'defer',
    ));
    wp_enqueue_script( 'cookie-js', $themeUrl . '/assets/js/cookie.js', array( 'jquery' ), filemtime($themeDir . '/assets/js/cookie.js'), array(
        'strategy' => 'defer',
    ));
    wp_enqueue_script( 'common-js', $themeUrl . '/assets/js/common.js', array( 'jquery' ), filemtime($themeDir . '/assets/js/common.js'), array(
        'strategy' => 'defer',
    ));

    // js only page, post
    if (is_front_page()) {
        wp_enqueue_script('slick-js', $themeUrl . '/assets/vendor/slick/slick.min.js', array('jquery'), '1.0', array(
            'strategy' => 'defer',
        ));
        wp_enqueue_script('top-js', $themeUrl . '/assets/js/top.js', array('jquery'), filemtime($themeDir . '/assets/js/top.js'), array(
            'strategy' => 'defer',
        ));
    }

    if(is_page(['mailformpro','contactform7'])){
        wp_enqueue_script( 'jquery-ui-js', $themeUrl . '/assets/vendor/jquery/jquery-ui.min.js', array( 'jquery' ), '1.0', array(
            'strategy' => 'defer',
        ));
        wp_enqueue_script( 'yubinbango-js', $themeUrl . '/assets/vendor/yubinbango/yubinbango.js', array( 'jquery' ), '1.0', array(
            'strategy' => 'defer',
        ));
        wp_enqueue_script('contact-js', $themeUrl . '/assets/js/contact.js', array('jquery'), filemtime($themeDir . '/assets/js/contact.js'), array(
            'strategy' => 'defer',
        ));
    }

} );             
?>