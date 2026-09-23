<?php 
AssetManager::css([
    'contact'
]);
AssetManager::js([
    'contact'
]);
get_header();
?>
<!-- ↓↓ main ↓↓ -->
<main class="p-contact">
    <section class="c-mv">
        <figure class="c-mv__bg">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_dummy.webp" alt="">
        </figure>
        <div class="l-container">
            <div class="c-mv__inner">
                <h1 class="c-mv-title">
                    <span class="c-mv-title__en">Contact</span>
                    <span class="c-mv-title__jp">お問い合わせ</span>
                </h1>
            </div>
        </div>
    </section>
    <div class="l-container">
        <ul class="c-bread">
            <li class="c-bread__item">
                <a href="" class="c-bread__link">top</a>
            </li>
            <li class="c-bread__item">お問い合わせ</li>
        </ul>
    </div>
    <div class="p-contact__inner">
        <div class="l-container">
            <?php echo do_shortcode('[contact-form-7 id="2f8607a" title="contact"]'); ?>
        </div>
    </div>
</main>
<!-- ↑↑ main ↑↑ -->
<?php
get_footer();
?>