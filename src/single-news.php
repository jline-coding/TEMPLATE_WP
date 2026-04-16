<?php get_header(); 
global $post;
$post_type = get_post_type( $post );
$post_type_obj = get_post_type_object( $post_type );
$cate = get_the_terms(get_the_ID(), 'news-cate'); 
?>
<main class="p-details">   
    <?php get_template_part('includes/template', 'bread'); ?>
    <div class="l-container">
        <section class="p-details__inner">
            <div class="p-details__head">
                <h1 class="c-ttl32"><?php echo esc_html(get_the_title()); ?></h1>
                <p class="p-details__date"><?php echo esc_html(get_the_date('Y.m.d')); ?></p>
                <?php if (!empty($cate) && is_array($cate)):?>
                <div class="p-details__cate">
                    <?php foreach ($cate as $term):?>
                    <a href="<?php echo esc_url(home_url('/news/category/'.$term->slug.'/')); ?>" class="p-details__cate__link"><?php echo esc_html($term->name); ?></a>
                    <?php endforeach; ?>  
                </div>
                <?php  endif; ?>
            </div>
            <?php $thumb_id = get_post_thumbnail_id(get_the_ID(), 'full'); ?>
            <figure class="p-details__thumb">
                <?php echo $thumb_id ? wp_get_attachment_image($thumb_id, 'full') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
            </figure>
            <div class="c-blockeditor">
                <?php echo do_blocks('<!-- wp:post-content /-->'); ?>
            </div>
            <div class="p-details__end">
                <div class="p-details__arrows">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    <?php if ( $prev_post || $next_post ) : ?>
                    <nav class="p-details__arrows" aria-label="Post navigation">

                        <?php if ( $prev_post ) : ?>
                            <div class="p-details__arrows__item">
                                <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" 
                                class="c-arrows" 
                                aria-label="Previous post">
                                    <svg class="c-arrows__icon" xmlns="http://www.w3.org/2000/svg" width="7.231" height="13.047" viewBox="0 0 7.231 13.047">
                                        <path d="M8929.162,5539.448l-6.171,6.17,6.171,6.17"
                                            transform="translate(-8922.284 -5539.095)"
                                            fill="none"
                                            stroke="#fff"
                                            stroke-width="1"/>
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ( $next_post ) : ?>
                            <div class="p-details__arrows__item">
                                <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" 
                                class="c-arrows c-arrows--next" 
                                aria-label="Next post">
                                    <svg class="c-arrows__icon" xmlns="http://www.w3.org/2000/svg" width="7.231" height="13.047" viewBox="0 0 7.231 13.047">
                                        <path d="M8929.162,5539.448l-6.171,6.17,6.171,6.17"
                                            transform="translate(-8922.284 -5539.095)"
                                            fill="none"
                                            stroke="#fff"
                                            stroke-width="1"/>
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>
                </div>
                <div class="p-details__btn">
                    <a href="<?php echo esc_url(home_url('/news/')); ?>" class="c-btn01 c-btn01--small">一覧へ戻る</a>
                </div>
            </div>
        </section>
    </div>
</main>
<?php get_footer(); ?>