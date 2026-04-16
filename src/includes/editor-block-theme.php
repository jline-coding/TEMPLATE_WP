<?php
///////////////////////////////////
// part block theme
/////////////////////////////////

function written_enqueue_block_variations() {

	wp_enqueue_script(
		'written-enqueue-block-variations',
		get_template_directory_uri() . '/assets/js/block-editor.js',
		array( 'wp-rich-text','wp-blocks', 'wp-dom-ready', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-primitives', 'lodash' ),
		wp_get_theme()->get( 'Version' ),
		false
	);
	wp_localize_script(
        'written-enqueue-block-variations',
        'themeData',
        array(
            'templateUrl' => get_template_directory_uri(),
        )
    );
}
add_action( 'enqueue_block_editor_assets', 'written_enqueue_block_variations' );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/blockeditor.css' );
});

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'mytheme-style',
		get_parent_theme_file_uri( '/assets/css/blockeditor.css' ),
		array( 'wp-block-library' ),
		null
	);
}, 20 );

// Editor UI
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'my-editor-ui-style',
        get_parent_theme_file_uri('/assets/css/blockeditor.css'),
        ['wp-edit-blocks'],
        null
    );
});

add_action('admin_enqueue_scripts', function() {
    wp_add_inline_style('acf-input', '
	div[data-name="works-imgs"] .acf-gallery-main{
		width: 50%;
	}
    div[data-name="works-imgs"] .acf-gallery-attachments {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
	div[data-name="works-imgs"] .acf-gallery-attachment{
		width: 100% !important;
	}

	div[data-name="works-imgs"] .acf-gallery-attachment.ui-sortable-helper{
		width: 300px !important;
	}
	.acf-editor-wrap div.mce-edit-area{
		padding: 20px;
	}
	.acf-editor-wrap div.mce-fullscreen{
		top:30px;
	}
	.mce-menu-item.mce-menu-item-preview.mce-active .mce-text, .mce-menu-item.mce-menu-item-preview.mce-active .mce-ico{
		color: #fff !important;
	}

    ');
});

add_filter('wp_terms_checklist_args', function($args) {
    $args['checked_ontop'] = false;
    return $args;
});


function my_add_editor_styles() {
    add_editor_style( 'assets/css/editorclassic.css' );
}
add_action( 'after_setup_theme', 'my_add_editor_styles' );

function my_mce_buttons( $buttons ) {

    array_unshift( $buttons, 'styleselect' );

    return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons' );

function rename_styleselect_label( $init ) {

	$items = array();

    for ( $i = 10; $i <= 32; $i++ ) {

        $items[] = array(
            'title'    => 'Text ' . $i . 'px',
            'selector' => 'p',
            'classes'  => 'c-txt' . $i,
        );
    }

    $init['style_formats'] = json_encode( array(
		array(
            'title' => 'Font Size',
            'items' => $items,
        ),
        array(
            'title' => 'Line Height',
            'items' => array_map( function( $i ) {

                $lh = $i / 10;

                return array(
                    'title'    => (string) $lh,
                    'selector' => 'p',
                    'styles'   => array(
                        'line-height' => (string) $lh,
                    ),
                );

            }, range( 10, 26 ) )
        )
    ));

    $init['style_formats_merge'] = false;

    return $init;
}
add_filter( 'tiny_mce_before_init', 'rename_styleselect_label' );
