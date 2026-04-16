<?php get_header(); 
$works_main_visual = get_option('works_main_visual');
$terms_works = get_terms([
    'taxonomy'   => 'works-cate',
    'hide_empty' => false,
]);
$is_term = get_queried_object();
$taxonomy_obj = get_taxonomy( $is_term->taxonomy );
$post_type = $taxonomy_obj->object_type[0] ?? null;
$post_type_object = get_post_type_object($post_type);
$paged = get_query_var('paged') ? get_query_var('paged') : 1;   
$args_works = array(
    'post_type' => 'works', 
    'posts_per_page' => $wp_query->query_vars['posts_per_page'],
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy' => 'works-cate',
            'field'    => 'term_id',
            'terms'    => $is_term->term_id,
        ),
    ),
);      
$query_works = new WP_Query($args_works);
?>
<main class="p-works">
    <?php get_template_part('includes/template', 'bread'); ?>
    <section class="c-mv">
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <?php 
                    $ttl_en = get_field('setup-works-title','options');
                    $thumb_id = get_field('setup-works-img-mv','options');
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
    <section class="p-works__cate">
        <div class="l-container">
            <p class="c-txt16">実際の施工事例を通じて、近畿トータルサービスの対応力と提案力をご覧いただけます。<br>課題にどう向き合い、どう解決したのか。そのプロセスに、私たちの強みがあります。</p>
            <h2 class="c-ttl28 c-ttl28--blue c-ttl28--firacon u-fw--400" data-aos="fade-up">CATEGORY</h2>
            <?php if(!empty($terms_works) && !is_wp_error($terms_works)): ?>
            <div class="c-list02 c-list02--flex">
                <?php foreach ($terms_works as $term): ?>
                <?php if ($term->parent == 0) : ?>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/works/category/'.$term->slug.'/')); ?>" class="c-btn01 <?php echo ($is_term->term_id == $term->term_id) ? "active" : ""; ?>"><?php echo esc_html($term->name); ?></a>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <div class="p-works__list">
        <?php if ($query_works->have_posts()) : ?>
        <ul class="c-listworks">
            <?php while ($query_works->have_posts()) : $query_works->the_post(); $cate = get_the_terms(get_the_ID(), 'works-cate');?>
            <li class="c-listworks__item" data-aos="fade-up">
                <a href="<?php the_permalink(); ?>" class="c-listworks__link">
                    <?php $thumb_id = get_post_thumbnail_id(get_the_ID(), 'full'); ?>
                    <div class="c-listworks__img">
                        <figure class="c-listworks__img__content">
                            <?php echo $thumb_id ? wp_get_attachment_image($thumb_id, 'full') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
                        </figure>
                    </div>
                    <div class="c-listworks__content">
                        <div class="c-listworks__head">
                            <?php if (!empty($cate) && is_array($cate)):?>
                                <div class="c-listworks__cate">
                                    <?php foreach ($cate as $term):?>
                                        <?php if ($term->parent == 0) : ?>
                                            <span class="c-listworks__cate__link">
                                                <?php echo esc_html($term->name); ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>  
                                </div>
                            <?php  endif; ?>
                            <p class="c-listworks__ttl"><?php echo esc_html(get_the_title()); ?></p>
                        </div>
                        <?php if (!empty($cate) && is_array($cate)):?>
                            <div class="c-listworks__catechild">
                                <?php foreach ($cate as $term):?>
                                    <?php if ($term->parent != 0) : ?>
                                        <span class="c-listworks__catechild__link">
                                            <?php echo esc_html($term->name); ?>
                                        </span>
                                    <?php endif; ?>
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
            wp_pagenavi(array('query' => $query_works)); 
            wp_reset_postdata();
        ?> 
    </div>
</main>
<?php get_footer(); ?>
