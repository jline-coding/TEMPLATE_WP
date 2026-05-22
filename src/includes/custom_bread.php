<?php 
if (!function_exists('mytheme_breadcrumb')) {

    function mytheme_breadcrumb() {

        $items = [];

        // Home
        $items[] = [
            'title' => 'HOME',
            'url'   => home_url('/'),
        ];

        // Page
        if (is_page()) {

            global $post;

            if ($post->post_parent) {

                $parents = array_reverse(get_post_ancestors($post->ID));

                foreach ($parents as $parent_id) {
                    $items[] = [
                        'title' => get_the_title($parent_id),
                        'url'   => get_permalink($parent_id),
                    ];
                }
            }

            $items[] = [
                'title' => get_the_title(),
                'url'   => '',
            ];
        }

        if (is_post_type_archive()) {
            $items[] = [
                'title' => post_type_archive_title('', false),
                'url'   => '',
            ];
        }

        // Single Post
        elseif (is_single()) {

            $post_type = get_post_type();

            // CPT archive
            if ($post_type !== 'post') {

                $items[] = [
                    'title' => post_type_archive_title('', false),
                    'url'   => get_post_type_archive_link($post_type),
                ];
            }

            $items[] = [
                'title' => get_the_title(),
                'url'   => '',
            ];
        }

        // Category
        elseif (is_category()) {

            $items[] = [
                'title' => single_cat_title('', false),
                'url'   => '',
            ];
        }

        // Taxonomy
        elseif (is_tax()) {

            $items[] = [
                'title' => single_term_title('', false),
                'url'   => '',
            ];
        }

        // Archive
        elseif (is_archive()) {

            $items[] = [
                'title' => get_the_archive_title(),
                'url'   => '',
            ];
        }

        elseif (is_404()) {

            $items[] = [
                'title' => "404 見つかりません",
                'url'   => '',
            ];
        }

        return $items;
    }
}