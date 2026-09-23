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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;700&family=Noto+Sans+JP:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <?php if (is_page('contact')): ?>
            <style>
                .grecaptcha-badge{
                    display: block !important;
                    z-index: 1000;
                }
            </style>
        <?php endif; ?>

        <?php if (is_front_page()): ?>
        <script>
            if (!sessionStorage.getItem('storage_loading')) {
                document.documentElement.classList.add('is-loading');
                sessionStorage.setItem('storage_loading', new Date().getTime().toString());
            }
        </script>
        <?php endif; ?>
    </head>
    <body>
        <?php wp_body_open(); ?>
        <div id="wrapper" class="l-wrapper">
            <header class="c-header">
                <div class="c-header__inner">  
                    <?php if(is_front_page()): ?>
                        <h1 class="c-header-logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="c-header-logo__link">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_logo.webp" alt="">
                            </a>
                        </h1>
                    <?php else: ?>
                        <div class="c-header-logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="c-header-logo__link">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_logo.webp" alt="">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="c-header__content">
                        <div class="c-gnavi">
                            <div class="c-gnavi__inner">
                                <ul class="c-gnavi-list">
                                    <li class="c-gnavi-list__item">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="c-gnavi-link">会社</a>
                                    </li>
                                    <li class="c-gnavi-list__item">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="c-gnavi-link">私たちについて</a>
                                    </li>
                                    <li class="c-gnavi-list__item is-sub">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="c-gnavi-link">サービス</a>
                                        <ul class="c-gnavi-sub">
                                            <li class="c-gnavi-sub__item">
                                                <a href="<?php echo esc_url(home_url('/')); ?>" class="c-gnavi-sub-link">サービス01</a>
                                            </li>
                                            <li class="c-gnavi-sub__item">
                                                <a href="<?php echo esc_url(home_url('/')); ?>" class="c-gnavi-sub-link">サービス02</a>
                                            </li>
                                            <li class="c-gnavi-sub__item">
                                                <a href="<?php echo esc_url(home_url('/')); ?>" class="c-gnavi-sub-link">サービス03</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="c-gnavi-list__item is-btn">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="c-btn c-btn--reverse">お問い合わせ</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="c-header-btns">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="c-btn c-btn--reverse c-header-btns__content">お問い合わせ</a>
                        </div>
                    </div>
                </div>
                <div class="c-toggle">
                    <span class="c-toggle__line"></span>
                </div>
            </header>