<?php 
/* Template Name: Thanks Recruit */
get_header();
?>
<!-- ↓↓ main ↓↓ -->
<main class="p-thanks">
    <section class="c-mv c-mv01">
        <ul class="c-bread">
            <li class="c-bread__item"><a href="<?php echo esc_url(home_url('/')); ?>" class="c-bread__link">近畿トータルサービス トップ</a></li>
            <li class="c-bread__item">エントリー送信完了</li>
        </ul>
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <h1 class="c-mv__title">
                    <span class="c-mv__title__jp"><?php echo esc_html(get_the_title()) ?></span>
                    <?php if(get_the_excerpt()): ?>
                    <span class="c-mv__title__en"><?php echo esc_html(get_the_excerpt()) ?></span>
                    <?php endif; ?>
                </h1>
            </div>
        </div>
    </section>
    <div class="p-thanks__main">
        <div class="l-container">
            <div class="p-thanks__inner">
                <h2 class="c-ttl28" data-aos="fade-up">エントリーいただきましてありがとうございます。</h2>
                <p data-aos="fade-up">
                    この度は、近畿トータルサービス株式会社 公式ホームページよりエントリー誠にありがとうございます。<br>
                    内容を確認の上、担当者より折り返しご連絡させていただきます。<br>
                    <br>
                    頂戴いたしましたお問い合わせにつきましては、内容を確認の上、後ほどご回答いたします。<br>
                    なお、お問い合わせの内容によっては、ご回答まで数日かかる場合やご回答いたしかねる場合がございます。<br>
                    恐れ入りますが、予めご了承くださいますようお願いいたします。
                </p>
                <div class="p-thanks__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="c-btn03">トップページへ戻る</a>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- ↑↑ main ↑↑ -->
<?php
get_footer();
?>