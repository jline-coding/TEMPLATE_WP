<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="format-detection" content="telephone=no, address=no, email=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="<?php echo get_site_icon_url(); ?>">
        <!-- link -->  
        <?php wp_head(); ?>
        <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/common/apple-touch-icon.webp" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noto+Sans+JP:wght@100..900&family=Shippori+Mincho:wght@400;500;600;700;800&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <?php 
            if(is_page('contact')){
                ?>
                <style>
                    .grecaptcha-badge{
                        display: block !important;
                        z-index: 1000;
                    }
                </style>
                <?php
            }
        ?>
    </head>
    <body>
        <?php if(is_front_page()): ?>
        <script>
            if (!window.sessionStorage.getItem("storage_loadding")) {
                document.documentElement.classList.add("is-loadding");    
                window.sessionStorage.setItem("storage_loadding", new Date().getTime().toString());    
            }
        </script>
        <div class="c-loading">
            <figure class="c-loading__bg">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/bg_loading.webp" alt="">
            </figure>
            <div class="c-loading__inner">
                <div class="c-loading__content">
                    <img class="c-loading__icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/common/icon_loading.svg" alt="">
                    <p class="c-loading__text">社員の志を、<br>企業の力に。<br>企業の挑戦を、<br>地球の豊かさに。 </p>
                </div>
            </div>
        </div>   
        <?php endif; ?>
        <div id="wrapper" class="body-wrapper ">
            <header class="c-header">
                <div class="c-header__inner">
                    <?php if(is_front_page()): ?>
                        <h1 class="c-header__logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="c-header__logo__link">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_logo.svg" alt="<?php echo get_bloginfo('name'); ?>">
                            </a>
                        </h1>
                    <?php else: ?>
                        <div class="c-header__logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="c-header__logo__link">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_logo.svg" alt="<?php echo get_bloginfo('name'); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="c-header__content">
                        <nav class="c-gnavi">
                            <ul class="c-gnavi__list">
                                <li class="c-gnavi__list__item">
                                    <a href="<?php echo esc_url(home_url('/business/')); ?>" class="c-gnavi__list__link">事業内容<svg class="c-gnavi__list__hover" xmlns="http://www.w3.org/2000/svg" width="5.613" height="9.812" viewBox="0 0 5.613 9.812"><path d="M3407.848,4598.272l4.2,4.2-4.2,4.2" transform="translate(-3407.141 -4597.565)" fill="none" stroke="#37557d" stroke-linecap="round" stroke-width="1"/></svg></a>
                                </li>
                                <li class="c-gnavi__list__item">
                                    <a href="<?php echo esc_url(home_url('/reason/')); ?>" class="c-gnavi__list__link">選ばれる理由<svg class="c-gnavi__list__hover" xmlns="http://www.w3.org/2000/svg" width="5.613" height="9.812" viewBox="0 0 5.613 9.812"><path d="M3407.848,4598.272l4.2,4.2-4.2,4.2" transform="translate(-3407.141 -4597.565)" fill="none" stroke="#37557d" stroke-linecap="round" stroke-width="1"/></svg></a>
                                </li>
                                <li class="c-gnavi__list__item">
                                    <a href="<?php echo esc_url(home_url('/works/')); ?>" class="c-gnavi__list__link">施工事例<svg class="c-gnavi__list__hover" xmlns="http://www.w3.org/2000/svg" width="5.613" height="9.812" viewBox="0 0 5.613 9.812"><path d="M3407.848,4598.272l4.2,4.2-4.2,4.2" transform="translate(-3407.141 -4597.565)" fill="none" stroke="#37557d" stroke-linecap="round" stroke-width="1"/></svg></a>
                                </li>
                                <li class="c-gnavi__list__item">
                                    <a href="<?php echo esc_url(home_url('/company/')); ?>" class="c-gnavi__list__link">企業情報<svg class="c-gnavi__list__hover" xmlns="http://www.w3.org/2000/svg" width="5.613" height="9.812" viewBox="0 0 5.613 9.812"><path d="M3407.848,4598.272l4.2,4.2-4.2,4.2" transform="translate(-3407.141 -4597.565)" fill="none" stroke="#37557d" stroke-linecap="round" stroke-width="1"/></svg></a>
                                </li>
                                <li class="c-gnavi__list__item">
                                    <a href="<?php echo esc_url(home_url('/news/')); ?>" class="c-gnavi__list__link">お知らせ<svg class="c-gnavi__list__hover" xmlns="http://www.w3.org/2000/svg" width="5.613" height="9.812" viewBox="0 0 5.613 9.812"><path d="M3407.848,4598.272l4.2,4.2-4.2,4.2" transform="translate(-3407.141 -4597.565)" fill="none" stroke="#37557d" stroke-linecap="round" stroke-width="1"/></svg></a>
                                </li>
                                <li class="c-gnavi__list__item">
                                    <a href="<?php echo esc_url(home_url('/recruit/')); ?>" class="c-gnavi__list__link">採用情報<svg class="c-gnavi__list__hover" xmlns="http://www.w3.org/2000/svg" width="5.613" height="9.812" viewBox="0 0 5.613 9.812"><path d="M3407.848,4598.272l4.2,4.2-4.2,4.2" transform="translate(-3407.141 -4597.565)" fill="none" stroke="#37557d" stroke-linecap="round" stroke-width="1"/></svg></a>
                                </li>
                                <li class="c-gnavi__list__item sp">
                                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="c-header__contact__btn u-clblue01">
                                        <span class="c-header__contact__ttl">お問い合わせ</span>
                                        <span class="c-header__contact__txt">＜企業＞</span>
                                    </a>
                                </li>
                                <li class="c-gnavi__list__item sp">
                                    <a href="<?php echo esc_url(home_url('/contact-g/')); ?>" class="c-header__contact__btn">
                                        <span class="c-header__contact__ttl">お問い合わせ</span>
                                        <span class="c-header__contact__txt">＜個人＞</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <div class="c-header__contact">
                            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="c-header__contact__btn u-clblue01">
                                <span class="c-header__contact__ttl">お問い合わせ</span>
                                <span class="c-header__contact__txt">＜企業＞</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/contact-g/')); ?>" class="c-header__contact__btn">
                                <span class="c-header__contact__ttl">お問い合わせ</span>
                                <span class="c-header__contact__txt">＜個人＞</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="c-toggle">
                    <span class="c-toggle__line"></span>
                </div>
            </header>
    