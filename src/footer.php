        <?php if(!is_page( ['contact', 'contact/confirm', 'contact/thanks'] )): ?>
        <section class="c-contact">
            <figure class="c-contact__bg">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/bg_contact.webp" alt="ノートパソコンの前でスマートフォンを操作する、オフィスカジュアルな服装の女性の手元">
            </figure>
            <div class="c-contact__inner">
                <div class="c-contact__content">
                    <h2 class="c-contact__title" data-aos="fade-up">
                        <span class="c-contact__title__jp">お問い合わせ</span>
                        <span class="c-contact__title__en">CONTACT</span>
                    </h2>
                    <p class="c-txt14" data-aos="fade-up">ご質問やご依頼など、<br>お問い合わせフォームよりお気軽にご相談ください。</p>
                </div>
                <div class="c-contact__btn">
                    <div class="c-contact__btn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="c-btn c-btn--white">
                            <span class="c-btn__content">企業の方はこちら</span>
                            <svg class="c-btn__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901"><path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#fff" stroke-width="1"/></svg>
                        </a>
                    </div>
                    <div class="c-contact__btn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/contact-g/')); ?>" class="c-btn c-btn--white">
                            <span class="c-btn__content">個人のお客様はこちら</span>
                            <svg class="c-btn__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901"><path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#fff" stroke-width="1"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        <footer class="c-footer">
            <div class="l-container">
                <div class="c-footer__logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="c-footer__logo__link">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_logo_footer.svg" alt="<?php echo get_bloginfo('name'); ?>">
                    </a>
                </div>
                <p class="c-ttl32">建築の悩みを<br class="sp">トータルサービスで解決。<br>技術と実績でお客様の<br class="sp">最適解をご提案。</p>
                <p class="c-ttl24">近畿トータルサービス株式会社</p>
                <p class="c-txt14">〒559-0024 大阪府大阪市住之江区新北島3-6-1</p>
                <p class="c-footer__tel"><a href="tel:06-6686-1121" class="c-tel">TEL：06-6686-1121</a></p>
                <ul class="c-footer__list">
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/business/')); ?>" class="c-footer__list__link">事業内容</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/reason/')); ?>" class="c-footer__list__link">選ばれる理由</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/works/')); ?>" class="c-footer__list__link">施工事例</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/company/')); ?>" class="c-footer__list__link">企業情報</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/news/')); ?>" class="c-footer__list__link">お知らせ</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/recruit/')); ?>" class="c-footer__list__link">採用情報</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/privacy/')); ?>" class="c-footer__list__link">プライバシーポリシー</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="c-footer__list__link">お問い合わせ（企業様）</a>
                    </li>
                    <li class="c-footer__list__item">
                        <a href="<?php echo esc_url(home_url('/contact-g/')); ?>" class="c-footer__list__link">お問い合わせ（個人のお客様）</a>
                    </li>
                </ul>
                <p class="c-footer__copy">© <?php echo date('Y'); ?> KINKI TOTAL SERVICE</p>
            </div>
        </footer>
        <a href="#wrapper" class="c-totop">totop</a>
        <div data-gdpr="wrap" id="cookiewrap">
            <div class="inner">
                <p class="close"><a href="javascript:void(0)" data-gdpr="button" style="display: inline-block;">同意する</a></p>
                <p class="txt">当サイトではCookieを使用します。Cookieの使用に関する詳細は「<a href="<?php echo is_page('privacy') ? "#ttl_policy" : esc_url(home_url( '/privacy/#ttl_policy' )); ?>">プライバシーポリシー</a>」をご覧ください。</p>
            </div>      
        </div>
        <!-- script -->  
        <?php wp_footer(); ?>
    </div>
    
  </body>
</html>