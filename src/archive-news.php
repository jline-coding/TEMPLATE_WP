<?php get_header(); 
$post_type = get_query_var('post_type');
$post_type_object = get_post_type_object($post_type);
$news_main_visual = get_option('news_main_visual');
$terms_news = get_terms([
    'taxonomy'   => 'news-cate',
    'hide_empty' => false,
]);
$paged = get_query_var('paged') ? get_query_var('paged') : 1;   
$args_news = array(
    'post_type' => 'news', 
    'posts_per_page' => $wp_query->query_vars['posts_per_page'],
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
);      
$query_news = new WP_Query($args_news);
?>
<main class="p-news">
    <?php get_template_part('includes/template', 'bread'); ?>
    <section class="c-mv">
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <?php 
                    $ttl_en = get_field('setup-news-title','options');
                    $thumb_id = get_field('setup-news-img-mv','options');
                ?>
                <h1 class="c-mv__title">
                    <span class="c-mv__title__jp"><?php echo esc_html( $post_type_object->labels->name ); ?></span>
                    <?php if($ttl_en): ?>
                    <span class="c-mv__title__en"><?php echo esc_html( $ttl_en ); ?></span>
                    <?php endif; ?>
                </h1>
            </div>
            <figure class="c-mv__img">
                <?php echo $thumb_id ? wp_get_attachment_image($thumb_id, 'full') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
            </figure>
        </div>
    </section>
    <section class="p-news__cate">
        <div class="l-container">
            <h2 class="c-ttl28 c-ttl28--blue c-ttl28--firacon u-fw--400" data-aos="fade-up">CATEGORY</h2>
            <?php if(!empty($terms_news) && !is_wp_error($terms_news)): ?>
            <div class="c-list03">
                <?php foreach ($terms_news as $term): ?>
                <div class="c-list03__item" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/news/category/'.$term->slug.'/')); ?>" class="c-btn01 c-btn01--mh60"><?php echo esc_html($term->name); ?></a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <div class="p-news__list">
        <?php if ($query_news->have_posts()) : ?>
        <ul class="c-listnews01">
            <?php while ($query_news->have_posts()) : $query_news->the_post(); $cate = get_the_terms(get_the_ID(), 'news-cate');?>
            <li class="c-listnews01__item" data-aos="fade-up">
                <a href="<?php the_permalink(); ?>" class="c-listnews01__link">
                    <?php $thumb_id = get_post_thumbnail_id(get_the_ID(), 'full'); ?>
                    <figure class="c-listnews01__img">
                        <?php echo $thumb_id ? wp_get_attachment_image($thumb_id, 'full') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
                    </figure>
                    <div class="c-listnews01__content">
                        <p class="c-listnews01__date"><?php echo esc_html(get_the_date('Y.m.d')); ?></p>
                        <p class="c-listnews01__ttl"><?php echo esc_html(get_the_title()); ?></p>
                        <?php if (!empty($cate) && is_array($cate)):?>
                            <div class="c-listnews01__cate">
                                <?php foreach ($cate as $term):?>
                                <span class="c-listnews01__cate__link"><?php echo esc_html($term->name); ?></span>
                                <?php endforeach; ?>  
                            </div>
                        <?php  endif; ?>
                    </div>
                </a>
            </li>
            <?php endwhile; ?>  
        </ul>
        <?php else: ?>
            <p class="c-txt16" data-aos="fade-up">現在記事がございません。</p>
        <?php endif; ?>
    </div>
    <div class="c-pagination" data-aos="fade-up">
        <?php
            wp_pagenavi(array('query' => $query_news)); 
            wp_reset_postdata();
        ?> 
    </div>
</main>
<?php get_footer(); ?>
