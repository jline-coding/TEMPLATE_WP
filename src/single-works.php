<?php get_header(); 
global $post;
$post_type = get_post_type( $post );
$post_type_obj = get_post_type_object( $post_type );
$cate = get_the_terms(get_the_ID(), 'works-cate'); 
?>
<main class="p-details">   
    <?php get_template_part('includes/template', 'bread'); ?>
    <section class="p-details__head01">
        <?php $thumb_id = get_post_thumbnail_id(get_the_ID(), 'full'); ?>
        <figure class="p-details__head01__img">
            <?php echo $thumb_id ? wp_get_attachment_image($thumb_id, 'full') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
        </figure>
        <div class="p-details__head01__content">
            <?php $ttl_en = get_field('setup-works-title','options'); ?>
            <h1 class="c-title c-title--small">
                <span class="c-title__jp"><?php echo $post_type_obj->labels->name; ?></span>
                <?php if($ttl_en): ?>
                <span class="c-title__en"><?php echo esc_html( $ttl_en ); ?></span>
                <?php endif; ?>
            </h1>
            <h2 class="c-ttl32"><?php echo esc_html(get_the_title()); ?></h2>
            <?php $works_description = get_field('works-description'); ?>
            <?php if($works_description): ?>
            <p class="c-txt16"><?php echo nl2br(esc_html($works_description)); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <div class="p-details__flex">
        <div class="p-details__sidebar">
            <?php if (!empty($cate) && is_array($cate)):?>
                <div class="p-details__catechild">
                    <?php foreach ($cate as $term):?>
                        <?php if ($term->parent != 0) : ?>
                            <a href="<?php echo esc_url(home_url('/works/category/'.$term->slug.'/')); ?>" class="p-details__catechild__link">
                                <?php echo esc_html($term->name); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>  
                </div>
            <?php  endif; ?>
            <?php $works_company = get_field('works-company'); ?>
            <?php if($works_company): ?>
            <p class="c-ttl20" data-aos="fade-up"><?php echo esc_html($works_company); ?></p>
            <?php endif; ?>
            <?php $works_location = get_field('works-location'); ?>
            <?php if($works_location): ?>
            <p class="c-txt16" data-aos="fade-up"><?php echo esc_html($works_location); ?></p>
            <?php endif; ?>
            <?php if (!empty($cate) && is_array($cate)):?>
                <div class="p-details__cate01" data-aos="fade-up">
                    <?php foreach ($cate as $term):?>
                        <?php if ($term->parent == 0) : ?>
                            <a href="<?php echo esc_url(home_url('/works/category/'.$term->slug.'/')); ?>" class="p-details__cate01__link">
                                <?php echo esc_html($term->name); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>  
                </div>
            <?php  endif; ?>
        </div>
        <div class="p-details__content">
            <?php $works_imgs = get_field('works-imgs'); ?>
            <?php if (!empty($works_imgs) && is_array($works_imgs)) : ?>
                <ul class="c-gallery">
                    <?php foreach ($works_imgs as $image_id) : ?>
                        <?php if (!empty($image_id)) : 
                            $caption = wp_get_attachment_caption($image_id);
                        ?>
                            <li class="c-gallery__item" data-aos="fade-up">
                                <?php echo wp_get_attachment_image((int)$image_id, 'full'); ?>

                                <?php if ($caption) : ?>
                                    <p class="c-gallery__caption">
                                        <?php echo esc_html($caption); ?>
                                    </p>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="l-container">
        <div class="p-details__btn01" data-aos="fade-up">
            <a href="<?php echo esc_url(home_url('/works/')); ?>" class="c-btn01 c-btn01--small">一覧へ戻る</a>
        </div>
        <div class="p-details__flex01">
            <div class="p-details__flex01__left">
                <div class="p-details__arrows01">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    <?php if ( $prev_post || $next_post ) : ?>
                    <nav class="p-details__arrows01" aria-label="Post navigation">
                        <?php if ( $prev_post ) : ?>
                            <div class="p-details__arrows01__item" data-aos="fade-up">
                                <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" 
                                class="c-arrows c-arrows--small" 
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
                            <div class="p-details__arrows01__item" data-aos="fade-up">
                                <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" 
                                class="c-arrows c-arrows--next c-arrows--small" 
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
            </div>
            <?php 
                $terms_works = get_terms([
                    'taxonomy'   => 'works-cate',
                    'hide_empty' => false,
                ]);
                $parent_terms = '';
                $child_terms  = '';

                if (!empty($terms_works) && !is_wp_error($terms_works)) {
                    foreach ($terms_works as $term) {
                        if ($term->parent == 0) {
                            $parent_terms .= '<li class="p-details__list__item" data-aos="fade-up"><a href="'.esc_url(home_url('/works/category/'.$term->slug.'/')).'" class="p-details__list__link">'.esc_html($term->name).'</a></li>';
                        } else {
                            $child_terms .= '<li class="p-details__list__item" data-aos="fade-up"><a href="'.esc_url(home_url('/works/category/'.$term->slug.'/')).'" class="p-details__list__link">'.esc_html($term->name).'</a></li>';
                        }
                    }
                }
            ?>
            <?php if(!empty($terms_works) && !is_wp_error($terms_works)): ?>
            <div class="p-details__flex01__right">
                <?php if($parent_terms): ?>
                <div class="p-details__cates">
                    <h3 class="c-ttl20 c-ttl20--blue c-ttl20--firacon u-fw--400" data-aos="fade-up">CATEGORY</h3>
                    <ul class="p-details__list"><?php echo $parent_terms; ?></ul>
                </div>
                <?php endif; ?>
                <?php if($child_terms): ?>
                <div class="p-details__tags">
                    <h3 class="c-ttl20 c-ttl20--blue c-ttl20--firacon u-fw--400" data-aos="fade-up">TAG</h3>
                    <ul class="p-details__list"><?php echo $child_terms; ?></ul>
                </div>
                <?php endif; ?>
            </div>
                <?php endif; ?>
        </div>
    </div>
</main>
<?php get_footer(); ?>