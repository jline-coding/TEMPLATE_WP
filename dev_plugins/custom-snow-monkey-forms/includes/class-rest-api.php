<?php
/**
 * Public, hash-protected frontend configuration endpoint.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_REST_API {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			'custom-snow-monkey-forms/v1',
			'/config/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_config' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id'             => array( 'sanitize_callback' => 'absint' ),
					'form_hash'      => array( 'sanitize_callback' => 'sanitize_text_field', 'required' => true ),
					'source_post_id' => array( 'sanitize_callback' => 'absint', 'default' => 0 ),
				),
			)
		);
	}

	/** @return WP_REST_Response|WP_Error */
	public function get_config( WP_REST_Request $request ) {
		$form_id        = absint( $request['id'] );
		$form_hash      = (string) $request->get_param( 'form_hash' );
		$source_post_id = absint( $request->get_param( 'source_post_id' ) );
		$post            = get_post( $form_id );

		if ( ! $post || 'snow-monkey-forms' !== $post->post_type || 'trash' === $post->post_status ) {
			return new WP_Error( 'csmf_form_not_found', 'フォームが見つかりません。', array( 'status' => 404 ) );
		}

		$meta_class = '\\Snow_Monkey\\Plugin\\Forms\\App\\Model\\Meta';
		$expected   = $meta_class::generate_form_hash( $form_id, $source_post_id );
		if ( ! $form_hash || ! hash_equals( $expected, $form_hash ) ) {
			return new WP_Error( 'csmf_invalid_form_hash', '無効なアクセスです。', array( 'status' => 403 ) );
		}

		$response = rest_ensure_response( CSMF_Config::for_frontend( $form_id ) );
		$response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private' );
		return $response;
	}
}
