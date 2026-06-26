<?php 

// Disable Contact Form 7 automatic paragraph tags
add_filter('wpcf7_autop_or_not', 'wpcf7_autop_return_false');
function wpcf7_autop_return_false() {
  return false;
}

//Validate furigana (katakana + hiragana - フリガナ + ふりがな)
function custom_wpcf7_validate_furigana($result, $tag)
{
    $tag   = new WPCF7_Shortcode($tag);
    $name  = $tag->name;
    $value = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : "";
    if ($name === "your-name-kana") {
        // Skip validation if empty — field is not required
        if (empty($value)) {
            return $result;
        }
        if (!preg_match('/^[ぁ-ゞァ-ヾ 　]*?[ぁ-ゞァ-ヾ]+?[ぁ-ゞァ-ヾ 　]*?$/u', $value)) {
            $result->invalidate($tag, "ひらがなかカタカナで入力してください。");
        }
    }
    return $result;
}

add_filter('wpcf7_validate_text*', 'custom_wpcf7_validate_furigana', 11, 2);
add_filter('wpcf7_validate_text', 'custom_wpcf7_validate_furigana', 11, 2);

// Email confirmation — verify that your-email and your-email-re match
add_filter('wpcf7_validate_email*', 'validate_email_confirm', 20, 2);
add_filter('wpcf7_validate_email', 'validate_email_confirm', 20, 2);
function validate_email_confirm($result, $tag) {
  if ($tag->name !== 'your-email-re') {
    return $result;
  }

  $email    = isset($_POST['your-email'])    ? trim(wp_unslash($_POST['your-email']))    : '';
  $email_re = isset($_POST['your-email-re']) ? trim(wp_unslash($_POST['your-email-re'])) : '';
  if ($email_re === '') {
    return $result; // Leave required validation to CF7
  }

  if ($email !== $email_re) {
    $result->invalidate($tag, 'メールアドレスが一致しません。');
  }

  return $result;
}

function custom_cf7_redirect_and_cookie() {

    $home = home_url();

    ?>
    <script>
    document.addEventListener('wpcf7mailsent', function(event) {

        var currentPath = window.location.pathname;
        var redirectUrl = '';
        var cookieName = '';

        if (/\/contactform7\//.test(currentPath) && !/\/thanks/.test(currentPath)) {
            redirectUrl = '<?php echo esc_url($home); ?>/contactform7/thanks/';
            cookieName = 'contact_sent';
        }

        if (redirectUrl !== '') {
            var expires = new Date();
            expires.setTime(expires.getTime() + (5 * 60 * 1000));
            document.cookie = cookieName + '=true; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
            window.location.href = redirectUrl;
        }

    }, false);
    </script>
    <?php
}
add_action('wp_footer', 'custom_cf7_redirect_and_cookie');