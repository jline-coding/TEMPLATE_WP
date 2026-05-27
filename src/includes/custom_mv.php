<?php 
if (!function_exists('mytheme_get_page_title')) {

    function mytheme_get_page_title() {

        $items = [];

        // Front page
        if (is_front_page()) {
            return get_bloginfo('name');
        }

        // Blog page
        if (is_home()) {
            return get_the_title(get_option('page_for_posts'));
        }

        // Single page
        if (is_page()) {
            $title = get_the_title();
            return $title;
        }

        // Single post
        if (is_single()) {
            return get_the_title();
        }

        // Category archive
        if (is_category()) {
            return single_cat_title('', false);
        }

        // Tag archive
        if (is_tag()) {
            return single_tag_title('', false);
        }

        // Custom taxonomy
        if (is_tax()) {
            return single_term_title('', false);
        }

        // CPT archive
        if (is_post_type_archive()) {
            return post_type_archive_title('', false);
        }

        // Date archive
        if (is_date()) {
            return get_the_archive_title();
        }

        // Author archive
        if (is_author()) {
            return get_the_archive_title();
        }

        // Search
        if (is_search()) {
            return '「'.get_search_query().'」の検索結果: ';
        }

        // 404
        if (is_404()) {
            return '404 見つかりません';
        }

        // Default archive
        if (is_archive()) {
            return get_the_archive_title();
        }

        return wp_get_document_title();
    }
}