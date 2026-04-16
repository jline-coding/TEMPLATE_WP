<?php

// 1. Khai báo post type + label 1 lần duy nhất
function get_archive_mv_post_types() {
    return [
        'news'  => 'お知らせ',
        'works' => '施工事例'
    ];
}


/* ===============================
   Admin Menu
================================= */
add_action('admin_menu', function () {

    $post_types = get_archive_mv_post_types();

    foreach ($post_types as $pt => $label) {

        add_submenu_page(
            'edit.php?post_type=' . $pt,
            $label . 'のアイキャッチ画像',
            'アイキャッチ画像',
            'manage_options',
            $pt . '-main-visual',
            function() use ($pt, $label) {
                render_main_visual_page($pt, $label);
            }
        );
    }
});


/* ===============================
   Render Page
================================= */
function render_main_visual_page($post_type, $label) {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html($label); ?>のアイキャッチ画像</h1>

        <form method="post" action="options.php">
            <?php
            settings_fields($post_type . '_mv_group');
            do_settings_sections($post_type . '-main-visual');
            submit_button('保存');
            ?>
        </form>
    </div>
    <?php
}


/* ===============================
   Register Settings
================================= */
add_action('admin_init', function () {

    $post_types = get_archive_mv_post_types();

    foreach ($post_types as $pt => $label) {

        register_setting(
            $pt . '_mv_group',
            $pt . '_main_visual'
        );

        add_settings_section(
            $pt . '_mv_section',
            '',
            null,
            $pt . '-main-visual'
        );

        add_settings_field(
            $pt . '_main_visual',
            'アイキャッチ画像',
            function() use ($pt) {
                main_visual_field_callback($pt);
            },
            $pt . '-main-visual',
            $pt . '_mv_section'
        );
    }
});


/* ===============================
   Media Field
================================= */
function main_visual_field_callback($post_type) {

    $option_name = $post_type . '_main_visual';
    $image_id = get_option($option_name);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>

    <div>
        <img id="<?php echo esc_attr($option_name); ?>_preview"
             src="<?php echo esc_url($image_url); ?>"
             style="max-width:300px; display:block; margin-bottom:10px;" />

        <input type="hidden"
               id="<?php echo esc_attr($option_name); ?>"
               name="<?php echo esc_attr($option_name); ?>"
               value="<?php echo esc_attr($image_id); ?>" />

        <button type="button"
                class="button upload-mv"
                data-target="<?php echo esc_attr($option_name); ?>">
            画像を選択
        </button>
    </div>

    <?php
}


/* ===============================
   Media Uploader Script
================================= */
add_action('admin_enqueue_scripts', function($hook) {

    if (strpos($hook, 'main-visual') === false) return;

    wp_enqueue_media();

    wp_add_inline_script('jquery-core', "
        jQuery(document).ready(function($){

            $('.upload-mv').on('click', function(e){
                e.preventDefault();

                var button = $(this);
                var target = button.data('target');

                var frame = wp.media({
                    title: '画像を選択',
                    button: { text: 'この画像を使用' },
                    multiple: false
                });

                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#' + target).val(attachment.id);
                    $('#' + target + '_preview').attr('src', attachment.url);
                });

                frame.open();
            });

        });
    ");
});