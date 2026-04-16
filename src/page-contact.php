<?php
get_header();
?>
<main class="p-contact">
    <section class="c-mv c-mv01">
        <?php get_template_part('includes/template', 'bread'); ?>
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <h1 class="c-mv__title">
                    <span class="c-mv__title__jp"><?php echo esc_html(get_the_title()) ?></span>
                    <?php if (get_the_excerpt()): ?>
                        <span class="c-mv__title__en"><?php echo esc_html(get_the_excerpt()) ?></span>
                    <?php endif; ?>
                </h1>
            </div>
        </div>
    </section>
    <div class="p-contact__main">
        <div class="l-container">
            <div class="p-contact__inner">
                <div class="p-contact__right">
                    <div class="p-contact__content">
                        <p class="p-contact__desc u-fw--500" data-aos="fade-up">
                            ご希望のご用件がございましたら、どうぞ遠慮なくお気軽にお問い合わせください。<br>
                            <br>
                            入力しましたら「内容を確認して送信する」ボタンをクリックしてください。
                        </p>
                        <div class="p-contact__info" data-aos="fade-up">
                            <h2 class="p-contact__info__lead">お電話でのお問い合わせ</h2>
                            <p class="p-contact__info__tel">
                                Tel.<a class="u-tel" href="tel:0666861121">06-6686-1121</a>
                            </p>
                            <p class="p-contact__info__note">※お問合せの際は、「ホームページを見た」と一言お伝えください。</p>
                        </div>
                    </div>
                </div>
                <div class="c-form" data-aos="fade-up">
                    <?php 
                        if (have_posts()) : while (have_posts()) : the_post(); 
                            the_content(); 
                        endwhile; endif; 
                    ?> 
                </div>
            </div>
        </div>
    </div>
</main>
<?php
get_footer();
?>