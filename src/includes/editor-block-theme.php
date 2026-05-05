<?php
///////////////////////////////////
// part block theme
/////////////////////////////////

function written_enqueue_block_variations() {
    $script_path = get_theme_file_path( '/assets/js/blockeditor-variations.js' );
    if ( ! file_exists( $script_path ) ) return;

    wp_enqueue_script(
        'written-enqueue-block-variations',
        get_theme_file_uri( '/assets/js/blockeditor-variations.js' ),
        array( 'wp-rich-text','wp-blocks', 'wp-dom-ready', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-primitives', 'lodash' ),
        filemtime( $script_path ), // Tự động xoá cache khi file thay đổi
        true // Load in footer (Tối ưu hiệu suất)
    );
    
    // Ngôn ngữ cho JS theo cấu hình của Admin hiện tại
    $locale = get_user_locale();
    if ( strpos( $locale, 'ja' ) === 0 ) {
        $i18n = array(
            'Default' => 'デフォルト', 'Text Color' => 'テキストカラー', 'Background Color' => '背景色', 'Font Family' => 'フォントファミリー',
            'Font Weight' => 'フォントウェイト', 'Font Size (px)' => 'フォントサイズ（px）', 'Padding Mode' => '内側余白の設定方法',
            'All (4 sides equal)' => '全体（4辺同じ）', 'Vertical / Horizontal' => '上下 / 左右', 'Individual (4 sides)' => '個別（4辺別々）', 'Padding (All px)' => '内側余白（全体・px）',
            'Top / Bottom' => '上下（Top / Bottom）', 'Left / Right' => '左右（Left / Right）', 'Line Height' => '行間（line-height）',
            'Enable Border' => '枠線を表示する', 'Border Color' => '枠線カラー', 'Border Width (px)' => '枠線の太さ（px）',
            'Border Settings Mode' => '枠線の設定モード', 'Border' => '枠線', 'Top Border' => '上枠線', 'Right Border' => '右枠線', 'Bottom Border' => '下枠線', 'Left Border' => '左枠線',
            'Color' => 'カラー', 'Width (px)' => '太さ (px)',
            'Column Width Settings' => '列幅設定', 'Clear' => 'クリア',
            'TH Style Settings' => 'TH スタイル設定', 'TD Style Settings' => 'TD スタイル設定',
            'THEAD Style Settings' => 'THEAD スタイル設定', 'TFOOT Style Settings' => 'TFOOT スタイル設定'
        );
    } elseif ( strpos( $locale, 'vi' ) === 0 ) {
        $i18n = array(
            'Default' => 'Mặc định', 'Text Color' => 'Màu chữ', 'Background Color' => 'Màu nền', 'Font Family' => 'Phông chữ',
            'Font Weight' => 'Độ dày chữ', 'Font Size (px)' => 'Cỡ chữ (px)', 'Padding Mode' => 'Cách căn lề trong (Padding)',
            'All (4 sides equal)' => 'Đều 4 cạnh', 'Vertical / Horizontal' => 'Trục Dọc / Ngang', 'Individual (4 sides)' => 'Chỉnh từng cạnh', 'Padding (All px)' => 'Độ rộng lề (px)',
            'Top / Bottom' => 'Trên / Dưới', 'Left / Right' => 'Trái / Phải', 'Line Height' => 'Khoảng cách dòng',
            'Enable Border' => 'Hiển thị viền', 'Border Color' => 'Màu viền', 'Border Width (px)' => 'Độ dày viền (px)',
            'Border Settings Mode' => 'Kiểu hiển thị viền', 'Border' => 'Viền', 'Top Border' => 'Viền trên', 'Right Border' => 'Viền phải', 'Bottom Border' => 'Viền dưới', 'Left Border' => 'Viền trái',
            'Color' => 'Màu', 'Width (px)' => 'Độ dày (px)',
            'Column Width Settings' => 'Độ rộng các cột', 'Clear' => 'Khôi phục',
            'TH Style Settings' => 'Tùy chỉnh ô Tiêu đề (TH)', 'TD Style Settings' => 'Tùy chỉnh ô Nội dung (TD)',
            'THEAD Style Settings' => 'Tùy chỉnh Đầu bảng (THEAD)', 'TFOOT Style Settings' => 'Tùy chỉnh Cuối bảng (TFOOT)'
        );
    } else {
        $i18n = array(); // Default is already English in JS
    }

    wp_localize_script(
        'written-enqueue-block-variations',
        'themeData',
        array(
            'templateUrl' => get_theme_file_uri(),
            'i18n' => $i18n
        )
    );
}
add_action( 'enqueue_block_editor_assets', 'written_enqueue_block_variations' );

add_action( 'after_setup_theme', function () {
    add_theme_support( 'editor-styles' );
    $editor_css = get_theme_file_path( '/assets/css/blockeditor.css' );
    if ( file_exists( $editor_css ) ) {
        add_editor_style( 'assets/css/blockeditor.css' );
    }
});

// Load block styles ở Front-end
add_action( 'wp_enqueue_scripts', function () {
    $css_path = get_theme_file_path( '/assets/css/blockeditor.css' );
    if ( ! file_exists( $css_path ) ) return;

    wp_enqueue_style(
        'mytheme-block-style', // Đổi tên handle rõ ràng hơn
        get_theme_file_uri( '/assets/css/blockeditor.css' ),
        array( 'wp-block-library' ),
        filemtime( $css_path ) // Tự động xoá cache
    );
}, 20 );

// Đăng ký Block Category mới
add_filter( 'block_categories_all', function( $categories ) {
    $locale = get_user_locale();
    $title = ( strpos( $locale, 'ja' ) === 0 ) ? 'ブロックカスタム' : ( ( strpos( $locale, 'vi' ) === 0 ) ? 'Khối Tùy Chỉnh' : 'Custom Blocks' );

    return array_merge(
        [
            [
                'slug'  => 'my-category-custom',
                'title' => $title,
                'icon'  => 'screenoptions',
            ],
        ],
        $categories
    );
} );

// Đăng ký Block Styles (Class tuỳ chỉnh)
add_action('init', function () {
    $locale = get_user_locale();
    $dot_label = ( strpos( $locale, 'ja' ) === 0 ) ? 'ドット' : ( ( strpos( $locale, 'vi' ) === 0 ) ? 'Dấu chấm' : 'Dot' );
    $style01_label = ( strpos( $locale, 'ja' ) === 0 ) ? 'スタイル 01' : ( ( strpos( $locale, 'vi' ) === 0 ) ? 'Kiểu 01' : 'Style 01' );

    register_block_style(
        'core/heading',
        [
            'name'  => 'dot',
            'label' => $dot_label
        ]
    );

    register_block_style(
        'core/button',
        [ 
            'name'  => 'style01',
            'label' => $style01_label
        ]
    );
});
