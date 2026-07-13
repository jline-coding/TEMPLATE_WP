<?php
// EN title: $args['en_title'] → excerpt → empty
$mv_en_title = '';
if ( ! empty( $args['en_title'] ) ) {
    $mv_en_title = $args['en_title'];
} elseif ( is_page() ) {
    $mv_en_title = get_the_excerpt();
}

// JP title: $args['jp_title'] → mytheme_get_page_title()
$mv_jp_title = ! empty( $args['jp_title'] )
    ? $args['jp_title']
    : mytheme_get_page_title();
?>
<section class="c-mv01">
    <div class="l-container">
        <div class="c-mv01__inner">
            <h1 class="c-heading01">
                <?php if ( $mv_en_title ) : ?>
                    <span class="c-ttl01__en"><?php echo wp_kses_post( convert_title_br($mv_en_title) ); ?></span>
                <?php endif; ?>
                <span class="c-ttl01__jp"><?php echo wp_kses_post( convert_title_br($mv_jp_title) ); ?></span>
            </h1>
        </div>
    </div>
    <figure class="c-mv01__decord01">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_mv01.webp" alt="">
    </figure>
</section>