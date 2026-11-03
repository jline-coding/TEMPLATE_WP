<?php
AssetManager::css([
    'top'
]);
AssetManager::js([
    'top'
]);
get_header();
?>
<main class="p-top">
    <div class="p-top__inner">
        test
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_dummy.webp" alt="">
        <a href="" class="c-btn c-btn--arrow">詳しく見る</a>
    </div>
</main>

<?php
get_footer();
?>