<?php 

// Contact Form 7の自動pタグ無効
add_filter('wpcf7_autop_or_not', 'wpcf7_autop_return_false');
function wpcf7_autop_return_false() {
  return false;
}

add_filter( 'wpcf7_form_tag', 'dynamic_year_list', 10, 2 );
function dynamic_year_list( $tag, $unused ) {
    if ( $tag['name'] != 'your-year' ) return $tag;

    $current_year = date('Y');
    $years = range($current_year, $current_year - 30);
    array_unshift($years, '例）2012');
    $tag['raw_values'] = $years;
    $tag['values'] = $years;
    $tag['labels'] = $years;
    
    return $tag;
}

add_filter( 'wpcf7_form_tag', 'dynamic_recruit_job_list', 10, 2 );
function dynamic_recruit_job_list( $tag, $unused ) {

    if ( $tag['name'] != 'your-job' ) return $tag;

    $args_tab = array(
        'post_type'      => 'recruit_tab',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );

    $query_tab = new WP_Query($args_tab);

    $values = array();
    $labels = array();

    // option mặc định (value rỗng)
    $values[] = '';
    $labels[] = '選択してください。';

    while ($query_tab->have_posts()) {
        $query_tab->the_post();
        $values[] = get_the_title();
        $labels[] = get_the_title();
    }

    wp_reset_postdata();

    $tag['values'] = $values;
    $tag['labels'] = $labels;

    return $tag;
}


function custom_limit_textarea_length($result, $tag)
{
    $tag = new WPCF7_FormTag($tag);
    $maxLength = 480;

    $textarea_content = isset($_POST['your-message']) ? trim($_POST['your-message']) : '';

    if (mb_strlen($textarea_content, 'UTF-8') > $maxLength) {
        $result->invalidate('your-content', $maxLength . '文字以内でご入力ください');
    }
    return $result;
}


//Validate furigana (katakana + hiragana - フリガナ + ふりがな)
function custom_wpcf7_validate_furigana($result, $tag)
{
    $tag   = new WPCF7_Shortcode($tag);
    $name  = $tag->name;
    $value = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : "";
    if ($name === "your-furi") {
        if (!preg_match('/^[ぁ-ゞァ-ヾ 　]*?[ぁ-ゞァ-ヾ]+?[ぁ-ゞァ-ヾ 　]*?$/u', $value)) {
            $result->invalidate($tag, "ひらがなかカタカナで入力してください。");
        }
    }
    return $result;
}

add_filter('wpcf7_validate_text*', 'custom_wpcf7_validate_furigana', 11, 2);

function custom_cf7_redirect_and_cookie() {

    $home = home_url();

    ?>
    <script>
    document.addEventListener('wpcf7mailsent', function(event) {

        var currentPath = window.location.pathname;
        var redirectUrl = '';
        var cookieName = '';

        if (currentPath.indexOf('/contact/') !== -1 && currentPath.indexOf('/thanks') === -1) {
            redirectUrl = '<?php echo esc_url($home); ?>/contact/thanks/';
            cookieName = 'contact_sent';
        }

        if (currentPath.indexOf('/contact-g/') !== -1 && currentPath.indexOf('/thanks') === -1) {
            redirectUrl = '<?php echo esc_url($home); ?>/contact-g/thanks/';
            cookieName = 'contact_g_sent';
        }

        if (currentPath.indexOf('/recruit/') !== -1 && currentPath.indexOf('/thanks') === -1) {
            redirectUrl = '<?php echo esc_url($home); ?>/recruit/thanks/';
            cookieName = 'recruit_sent';
        }

        if (redirectUrl !== '') {

            var expires = new Date();
            expires.setTime(expires.getTime() + (5 * 60 * 1000));
            document.cookie = cookieName + "=true; expires=" + expires.toUTCString() + "; path=/";

            window.location.href = redirectUrl;
        }

    }, false);
    </script>
    <?php
}
add_action('wp_footer', 'custom_cf7_redirect_and_cookie');

function block_direct_thanks_access() {

    $uri = $_SERVER['REQUEST_URI'];

    if (strpos($uri, '/contact/thanks') !== false) {
        if (!isset($_COOKIE['contact_sent'])) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }

    if (strpos($uri, '/contact-g/thanks') !== false) {
        if (!isset($_COOKIE['contact_g_sent'])) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }

    if (strpos($uri, '/recruit/thanks') !== false) {
        if (!isset($_COOKIE['recruit_sent'])) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }

}
add_action('template_redirect', 'block_direct_thanks_access');
