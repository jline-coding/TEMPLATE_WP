<?php
/**
 * Conditional data cleanup.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$form_ids = get_posts(
	array(
		'post_type'      => 'snow-monkey-forms',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);

foreach ( $form_ids as $form_id ) {
	$config = get_post_meta( $form_id, '_csmf_config', true );
	if ( is_array( $config ) && ! empty( $config['delete_on_uninstall'] ) ) {
		delete_post_meta( $form_id, '_csmf_config' );
	}
}
