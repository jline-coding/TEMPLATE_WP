<?php
include_once('includes/editor-block-theme.php');
include_once('includes/styles-scripts-all.php');
include_once('includes/contactform.php');
include_once('includes/custom-mv-postype.php');
// ================ DEFAULT SETTING ===================
//add Featured Image
add_theme_support( 'post-thumbnails' );

//remove_filter( 'the_excerpt', 'wpautop' );
/*increa limit upload file*/
@ini_set( 'upload_max_filesize' , '64M' );
@ini_set( 'upload_max_size', '64M' );
@ini_set( 'memory_limit', '256M' );
@ini_set( 'post_max_size', '64M' );
@ini_set( 'max_execution_time', '300' );
@ini_set( 'max_input_time', '300' );
/*--add feature images--*/

/**
 * Allow SVG & ICO upload (Admin only)
 */
function allow_svg_ico_mimes( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['ico'] = 'image/x-icon';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_ico_mimes' );

/**
 * Fix MIME check for SVG & ICO
 */
function fix_svg_ico_mime_check( $data, $file, $filename, $mimes ) {

    // Only allow admin
    if ( ! current_user_can( 'manage_options' ) ) {
        return $data;
    }

    $filetype = wp_check_filetype( $filename, $mimes );

    if ( in_array( $filetype['ext'], [ 'svg', 'ico' ], true ) ) {
        $data['ext']  = $filetype['ext'];
        $data['type'] = $filetype['type'];
    }

    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'fix_svg_ico_mime_check', 10, 4 );

//ADD MENU
if ( function_exists( 'register_nav_menu' ) ) {
    register_nav_menu( 'main-menu', 'Main Menu' );
}
//EXCERPT
add_post_type_support( 'page', 'excerpt' );

require_once( dirname( __FILE__ ) . '/includes/shortcode.php' );
// ================ END DEFAULT SETTING ===================


// Prevent WP from adding <p> tags on pages
function disable_wp_auto_p( $content ) {
    if ( is_singular( 'page' ) ) {
        remove_filter( 'the_content', 'wpautop' );
        remove_filter( 'the_excerpt', 'wpautop' );
    }
    return $content;
}
add_filter( 'the_content', 'disable_wp_auto_p', 0 );

//EXCERPT
add_post_type_support( 'page', 'excerpt' );

// Prevent WP from adding <p> tags on pages by Contact form 7
add_filter('wpcf7_autop_or_not', '__return_false');


add_filter('show_admin_bar', function($show) {
    if (!is_admin()) {
        return false;
    }
    return $show;
});

function target_main_category_query_with_conditional_tags( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {       
		if ( is_post_type_archive('news') || is_tax('news-cate') ) {
			$query->set( 'posts_per_page', 12 );
		} 
		if ( is_post_type_archive('works') || is_tax('works-cate') ) {
			$query->set( 'posts_per_page', 12 );
		} 
   
	}
}
add_action( 'pre_get_posts', 'target_main_category_query_with_conditional_tags' );

function overwrite_ssp_title($ssp_title) { 
    if ( is_page() ) {
        global $post;
        if ( $post->post_parent ) {
            $parent_title = get_the_title( $post->post_parent );
            if(is_page(['contact/thanks'])){
              return "お問い合わせありがとうございました｜お問い合わせ－ 企業様用 －｜".get_bloginfo('name');
            }elseif(is_page(['contact-g/thanks'])){
              return "お問い合わせありがとうございました｜お問い合わせ－ 個人のお客様 －｜".get_bloginfo('name');
            }elseif(is_page(['recruit/thanks'])){
              return "エントリー完了｜".$parent_title."｜".get_bloginfo('name');
            }else{
              return str_replace(
                  '｜' . get_bloginfo('name'),
                  '｜' . $parent_title . '｜' . get_bloginfo('name'),
                  $ssp_title
              );
            }
        }

        if(is_page(['contact'])){
          return "お問い合わせ－ 企業様用 －｜".get_bloginfo('name');
        }elseif(is_page(['contact-g'])){
          return "お問い合わせ－ 個人のお客様 －｜".get_bloginfo('name');
        }
        
    }

    if ( is_tax() ) {

        $post_type = get_query_var('post_type');

        if ( empty($post_type) ) {
            $taxonomy = get_queried_object();
            $post_types = get_taxonomy($taxonomy->taxonomy)->object_type;
            $post_type = $post_types[0] ?? '';
        }

        if ( $post_type ) {
            $post_type_object = get_post_type_object($post_type);

            if ( $post_type_object ) {
                $post_type_label = $post_type_object->labels->name;
                $term = get_queried_object();

                return $term->name . "一覧｜" . $post_type_label . "｜" . get_bloginfo('name');
            }
        }
    }

    if ( is_single() ) {

      global $post;

      $post_type = get_post_type($post);
      $post_type_object = get_post_type_object($post_type);

      if ( $post_type_object ) {
          $post_type_label = $post_type_object->labels->name;

          return get_the_title($post) . "｜" . $post_type_label . "｜" . get_bloginfo('name');
      }
    }

    return $ssp_title;
}
add_filter('ssp_output_title', 'overwrite_ssp_title');



// サイドメニューを非表示
function remove_menus() {
  $current_user = wp_get_current_user();
  if ($current_user->user_login == 'writer-kinkitotalservice') {
    remove_menu_page('edit.php'); // 投稿
    remove_menu_page('upload.php'); // メディア
    remove_menu_page('edit.php?post_type=page'); // 固定ページ
    remove_menu_page('edit-comments.php'); // コメント
    remove_menu_page('themes.php'); // 外観
    remove_menu_page('plugins.php'); // プラグイン
    remove_submenu_page('index.php', 'update-core.php');
    remove_menu_page('users.php'); // ユーザー
    remove_menu_page('tools.php'); // ツール
    remove_menu_page('options-general.php'); // 設定
    remove_menu_page('wpcf7'); // contactform7
    remove_menu_page('ai1wm_export'); // wp migration
    remove_menu_page('cptui_main_menu'); // CPT UI
    remove_menu_page('siteguard'); // site Guard
  }
}
add_action('admin_menu', 'remove_menus');

// ダッシュボードを非表示
function remove_dashboard_widgets() {
  $current_user = wp_get_current_user();
  if ($current_user->user_login == 'writer-kinkitotalservice') {
    remove_action('admin_notices', 'update_nag', 3);
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); // サイトヘルスステータス
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // アクティビティ
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // 概要
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // クイックドラフト
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress イベントとニュース
  }
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

// 管理画面上部ツールバーに更新アイコンを非表示
function hide_adminbar_update_icon() {
  $current_user = wp_get_current_user();
  if ($current_user->user_login == 'writer-kinkitotalservice') {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
  }
}
add_action('wp_before_admin_bar_render', 'hide_adminbar_update_icon');

// 更新通知非表示
function update_message_admin_only() {
  $current_user = wp_get_current_user();
  if ($current_user->user_login == 'writer-kinkitotalservice') {
    add_filter('pre_site_transient_update_core', '__return_zero');
    remove_action('wp_version_check', 'wp_version_check');
    remove_action('admin_init', '_maybe_update_core');
  }
}
add_action('admin_init', 'update_message_admin_only');


