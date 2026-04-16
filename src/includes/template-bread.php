<?php
$breadcrumb_items = [];

/**
 * HOME
 */
$breadcrumb_items[] = [
    'url'  => home_url('/'),
    'text' => '近畿トータルサービス トップ',
    'current' => false
];


/**
 * TAXONOMY
 * posttype / term_name
 */
if (is_tax() || is_category() || is_tag()) {

    $term = get_queried_object();
    $taxonomy = get_taxonomy($term->taxonomy);

    if (!empty($taxonomy->object_type[0])) {

        $post_type = $taxonomy->object_type[0];
        $post_type_obj = get_post_type_object($post_type);

        // Post type archive
        $breadcrumb_items[] = [
            'url'  => get_post_type_archive_link($post_type),
            'text' => $post_type_obj->labels->name,
            'current' => false
        ];
    }

    // Term name
    $breadcrumb_items[] = [
        'url'  => '',
        'text' => $term->name,
        'current' => true
    ];
}


/**
 * SINGLE
 * posttype / title
 */
elseif (is_singular() && !is_page()) {

    global $post;

    $post_type = get_post_type($post->ID);
    $post_type_obj = get_post_type_object($post_type);

    // Post type archive
    if ($post_type_obj && $post_type_obj->has_archive) {
        $breadcrumb_items[] = [
            'url'  => get_post_type_archive_link($post_type),
            'text' => $post_type_obj->labels->name,
            'current' => false
        ];
    }

    // Title
    $breadcrumb_items[] = [
        'url'  => '',
        'text' => get_the_title(),
        'current' => true
    ];
}


/**
 * PAGE
 */
elseif (is_page()) {

    global $post;

    $parents = array_reverse(get_post_ancestors($post->ID));

    foreach ($parents as $parent_id) {
        $breadcrumb_items[] = [
            'url'  => get_permalink($parent_id),
            'text' => get_the_title($parent_id),
            'current' => false
        ];
    }

    $breadcrumb_items[] = [
        'url'  => '',
        'text' => get_the_title(),
        'current' => true
    ];
}
elseif (is_post_type_archive()) {

    $post_type = get_query_var('post_type');
    $post_type_obj = get_post_type_object($post_type);

    if ($post_type_obj) {
        $breadcrumb_items[] = [
            'url'  => '',
            'text' => $post_type_obj->labels->name,
            'current' => true
        ];
    }
}
?>

<?php if (!empty($breadcrumb_items)) : ?>
<ul class="c-bread">
    <?php foreach ($breadcrumb_items as $item) : ?>
    <li class="c-bread__item"><?php if (!$item['current']) : ?><a href="<?php echo esc_url($item['url']); ?>" class="c-bread__link"><?php echo esc_html($item['text']); ?></a><?php else : ?><span class="c-bread__link"><?php echo esc_html($item['text']); ?></span><?php endif; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
