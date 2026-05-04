<?php
require_once( get_template_directory() . '/includes/editor-block-theme.php' );
require_once( get_template_directory() . '/includes/styles-scripts-all.php' );
require_once( get_template_directory() . '/includes/contactform.php' );
require_once( get_template_directory() . '/includes/shortcode.php' );
//add Featured Image
add_theme_support( 'post-thumbnails' );

/*--add feature images--*/
/**
 * Allow SVG & ICO upload (Admin only)
 * ⚠️ Note: For production, consider using the "Safe SVG" plugin
 *    to sanitize SVG content and prevent XSS attacks.
 */
function allow_svg_ico_mimes( $mimes ) {
    // Only allow admin to upload SVG/ICO
    if ( ! current_user_can( 'manage_options' ) ) {
        return $mimes;
    }
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


add_filter('show_admin_bar', function($show) {
    if (!is_admin()) {
        return false;
    }
    return $show;
});

// Check permissions to hide admin features
function is_restricted_admin_user() {
    $user = wp_get_current_user();
    if ( ! $user || ! $user->exists() ) {
        return false;
    }

    // List of restricted roles (Default is Editor, you can change or add other roles here)
    $restricted_roles = array(
        'editor',
    );

    // Check by Role
    if ( array_intersect( $restricted_roles, (array) $user->roles ) ) {
        return true;
    }

    return false;
}

// Hide site menus
function remove_menus() {
  if ( is_restricted_admin_user() ) {
    remove_menu_page('ai1wm_export'); // wp migration
    remove_menu_page('cptui_main_menu'); // CPT UI
    remove_menu_page('edit-comments.php'); // comment
    remove_menu_page('edit.php'); // post
    remove_menu_page('edit.php?post_type=page'); // pages
    remove_menu_page('options-general.php'); // setting
    remove_menu_page('plugins.php'); // plugin
    remove_menu_page('siteguard'); // site Guard
    remove_menu_page('themes.php'); // appearance
    remove_menu_page('tools.php'); // tool
    remove_menu_page('upload.php'); // media
    remove_menu_page('users.php'); // user
    remove_menu_page('wpcf7'); // contactform7
    remove_submenu_page('index.php', 'update-core.php');
  }
}
// Set priority 999 to ensure it overrides menus added by plugins
add_action('admin_menu', 'remove_menus', 999);

// Hide dashboard widgets
function remove_dashboard_widgets() {
  if ( is_restricted_admin_user() ) {
    remove_action('admin_notices', 'update_nag', 3);
    remove_action('network_admin_notices', 'update_nag', 3); // Standard: Hide network update nag (if multisite is enabled)
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // activity
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress event and news
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // quick press
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // at a glance
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); // site health status
  }
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets', 999);

// Hide update icon in the top admin toolbar
function hide_adminbar_update_icon() {
  if ( is_restricted_admin_user() ) {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
  }
}
add_action('wp_before_admin_bar_render', 'hide_adminbar_update_icon', 999);

// Hide update notifications (PHP 8 Safe & Standard)
function update_message_admin_only() {
  if ( is_restricted_admin_user() ) {
    add_filter('pre_site_transient_update_core', '__return_null'); // WP Standard: return null instead of zero
    remove_action('admin_init', '_maybe_update_core');
    remove_action('wp_version_check', 'wp_version_check');
  }
}
add_action('admin_init', 'update_message_admin_only', 999);

// Block direct URL access to hidden pages
function block_direct_access_to_hidden_pages() {
    if ( is_restricted_admin_user() ) {
        global $pagenow;
        
        // List of restricted default WordPress pages
        $restricted_pages = array(
            'edit-comments.php',
            'options-general.php',
            'plugins.php',
            'themes.php',
            'tools.php',
            'upload.php',
            'users.php',
            'update-core.php'
        );

        if ( in_array( $pagenow, $restricted_pages, true ) ) {
            wp_redirect( admin_url() );
            exit;
        }

        // Special handling for edit.php (only block Post and Page access)
        if ( $pagenow === 'edit.php' ) {
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
            if ( $post_type === 'post' || $post_type === 'page' ) {
                wp_redirect( admin_url() );
                exit;
            }
        }

        // Block plugin pages
        if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) ) {
            $restricted_plugin_pages = array(
                'ai1wm_export',
                'cptui_main_menu',
                'siteguard',
                'wpcf7'
            );
            if ( in_array( $_GET['page'], $restricted_plugin_pages, true ) ) {
                wp_redirect( admin_url() );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'block_direct_access_to_hidden_pages', 999 );