<?php
/**
 * Configuration storage and sanitization.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_Config {
	const META_KEY = '_csmf_config';

	/**
	 * Default configuration.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'enabled'          => true,
			'realtime'         => true,
			'validate_on'      => 'blur_input',
			'hide_error_empty' => true,
			'focus_first_error'=> true,
			'clear_hidden'     => true,
			'route_mode'       => 'first_match',
			'delete_on_uninstall' => false,
			'validations'      => array(),
			'field_rules'      => array(),
			'recipient_rules'  => array(),
			'uploads'          => array(),
		);
	}

	/**
	 * Get normalized configuration for a form.
	 *
	 * @param int $form_id Form post ID.
	 * @return array<string,mixed>
	 */
	public static function get( $form_id ) {
		$form_id = absint( $form_id );
		$config  = get_post_meta( $form_id, self::META_KEY, true );
		$config  = is_array( $config ) && $config ? $config : self::legacy_config( $form_id );
		$config  = self::sanitize( wp_parse_args( $config, self::defaults() ) );
		return apply_filters( 'csmf/config', $config, $form_id );
	}

	/**
	 * Save a form configuration.
	 *
	 * @param int                 $form_id Form post ID.
	 * @param array<string,mixed> $config  Raw configuration.
	 * @return bool|int
	 */
	public static function save( $form_id, array $config ) {
		return update_post_meta( absint( $form_id ), self::META_KEY, self::sanitize( $config ) );
	}

	/**
	 * Return only values safe and necessary for the browser.
	 * Recipient addresses and server settings are intentionally excluded.
	 *
	 * @param int $form_id Form post ID.
	 * @return array<string,mixed>
	 */
	public static function for_frontend( $form_id ) {
		$config = self::get( $form_id );
		$frontend = array(
			'enabled'           => $config['enabled'],
			'realtime'          => $config['realtime'],
			'validateOn'        => $config['validate_on'],
			'hideErrorEmpty'    => $config['hide_error_empty'],
			'focusFirstError'   => $config['focus_first_error'],
			'clearHidden'       => $config['clear_hidden'],
			'validations'       => $config['validations'],
			'fieldRules'        => $config['field_rules'],
			'uploads'           => array_map(
				function ( $rule ) {
					unset( $rule['attach_admin'], $rule['attach_reply'] );
					return $rule;
				},
				$config['uploads']
			),
		);
		return apply_filters( 'csmf/frontend_config', $frontend, absint( $form_id ) );
	}

	/**
	 * Sanitize the complete schema.
	 *
	 * @param array<string,mixed> $raw Raw configuration.
	 * @return array<string,mixed>
	 */
	public static function sanitize( array $raw ) {
		$defaults = self::defaults();
		$clean    = array(
			'enabled'             => ! empty( $raw['enabled'] ),
			'realtime'            => ! empty( $raw['realtime'] ),
			'validate_on'         => in_array( $raw['validate_on'] ?? '', array( 'blur', 'input', 'blur_input' ), true ) ? $raw['validate_on'] : $defaults['validate_on'],
			'hide_error_empty'    => ! empty( $raw['hide_error_empty'] ),
			'focus_first_error'   => ! empty( $raw['focus_first_error'] ),
			'clear_hidden'        => ! empty( $raw['clear_hidden'] ),
			'route_mode'          => in_array( $raw['route_mode'] ?? '', array( 'first_match', 'merge_all' ), true ) ? $raw['route_mode'] : $defaults['route_mode'],
			'delete_on_uninstall' => ! empty( $raw['delete_on_uninstall'] ),
			'validations'         => array(),
			'field_rules'         => array(),
			'recipient_rules'     => array(),
			'uploads'             => array(),
		);

		foreach ( self::array_value( $raw, 'validations' ) as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}
			$field = self::field_name( $rule['field'] ?? '' );
			$type  = sanitize_key( $rule['type'] ?? '' );
			if ( ! $field || ! in_array( $type, self::validation_types(), true ) ) {
				continue;
			}
			$clean['validations'][] = array(
				'id'      => self::rule_id( $rule['id'] ?? '' ),
				'enabled' => ! isset( $rule['enabled'] ) || ! empty( $rule['enabled'] ),
				'field'   => $field,
				'type'    => $type,
				'param'   => self::plain( $rule['param'] ?? '', 500 ),
				'message' => sanitize_text_field( $rule['message'] ?? '' ),
			);
		}

		foreach ( self::array_value( $raw, 'field_rules' ) as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}
			$target = self::field_name( $rule['target'] ?? '' );
			if ( ! $target ) {
				continue;
			}
			$scope = in_array( $rule['scope'] ?? '', array( 'item', 'field' ), true ) ? $rule['scope'] : 'item';
			$clean['field_rules'][] = array(
				'id'         => self::rule_id( $rule['id'] ?? '' ),
				'enabled'    => ! isset( $rule['enabled'] ) || ! empty( $rule['enabled'] ),
				'target'     => $target,
				'action'     => 'hide_when' === ( $rule['action'] ?? '' ) ? 'hide_when' : 'show_when',
				'scope'      => $scope,
				'relation'   => 'any' === ( $rule['relation'] ?? '' ) ? 'any' : 'all',
				'conditions' => self::sanitize_conditions( $rule['conditions'] ?? array() ),
			);
		}

		foreach ( self::array_value( $raw, 'recipient_rules' ) as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}
			$to = self::email_templates( $rule['to'] ?? '' );
			if ( ! $to ) {
				continue;
			}
			$clean['recipient_rules'][] = array(
				'id'             => self::rule_id( $rule['id'] ?? '' ),
				'enabled'        => ! isset( $rule['enabled'] ) || ! empty( $rule['enabled'] ),
				'label'          => sanitize_text_field( $rule['label'] ?? '' ),
				'priority'       => max( 0, min( 9999, absint( $rule['priority'] ?? 10 ) ) ),
				'relation'       => 'any' === ( $rule['relation'] ?? '' ) ? 'any' : 'all',
				'conditions'     => self::sanitize_conditions( $rule['conditions'] ?? array() ),
				'to'             => $to,
				'cc'             => self::email_templates( $rule['cc'] ?? '' ),
				'bcc'            => self::email_templates( $rule['bcc'] ?? '' ),
				'reply_to'       => self::email_templates( $rule['reply_to'] ?? '' ),
				'subject_prefix' => sanitize_text_field( $rule['subject_prefix'] ?? '' ),
			);
		}

		foreach ( self::array_value( $raw, 'uploads' ) as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}
			$field = self::field_name( $rule['field'] ?? '' );
			if ( ! $field ) {
				continue;
			}
			$extensions = preg_split( '/[\s,]+/', strtolower( (string) ( $rule['extensions'] ?? 'jpg,jpeg,png,webp,pdf' ) ) );
			$extensions = array_values( array_unique( array_filter( array_map( 'sanitize_key', $extensions ) ) ) );
			$extensions = array_values( array_intersect( $extensions, array_keys( self::allowed_mimes() ) ) );
			$clean['uploads'][] = array(
				'id'           => self::rule_id( $rule['id'] ?? '' ),
				'enabled'      => ! isset( $rule['enabled'] ) || ! empty( $rule['enabled'] ),
				'field'        => $field,
				'required'     => ! empty( $rule['required'] ),
				'extensions'   => implode( ',', $extensions ?: array( 'jpg', 'jpeg', 'png', 'webp', 'pdf' ) ),
				'max_mb'       => max( 0.1, min( 100, (float) ( $rule['max_mb'] ?? 5 ) ) ),
				'min_width'    => absint( $rule['min_width'] ?? 0 ),
				'max_width'    => absint( $rule['max_width'] ?? 0 ),
				'min_height'   => absint( $rule['min_height'] ?? 0 ),
				'max_height'   => absint( $rule['max_height'] ?? 0 ),
				'attach_admin' => ! isset( $rule['attach_admin'] ) || ! empty( $rule['attach_admin'] ),
				'attach_reply' => ! empty( $rule['attach_reply'] ),
			);
		}

		return $clean;
	}

	/** @return string[] */
	public static function validation_types() {
		return array( 'required', 'email', 'tel_jp', 'postal_jp', 'hiragana', 'katakana', 'url', 'numeric', 'min_length', 'max_length', 'min', 'max', 'regex', 'equals_field', 'different_field' );
	}

	/** @return array<string,string> */
	public static function allowed_mimes() {
		return array(
			// Images
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'gif'  => 'image/gif',
			'webp' => 'image/webp',
			'avif' => 'image/avif',
			'svg'  => 'image/svg+xml',
			// Documents
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xls'  => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'ppt'  => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'txt'  => 'text/plain',
			'csv'  => 'text/csv',
			'zip'  => 'application/zip',
			'rar'  => 'application/x-rar-compressed',
		);
	}

	/** @return array<string,string> */
	public static function image_mimes() {
		return array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'gif'  => 'image/gif',
			'webp' => 'image/webp',
			'avif' => 'image/avif',
			'svg'  => 'image/svg+xml',
		);
	}

	/** @return array<int,array<string,string>> */
	private static function sanitize_conditions( $conditions ) {
		$clean = array();
		foreach ( is_array( $conditions ) ? $conditions : array() as $condition ) {
			if ( ! is_array( $condition ) ) {
				continue;
			}
			$field    = self::field_name( $condition['field'] ?? '' );
			$operator = sanitize_key( $condition['operator'] ?? '' );
			if ( ! $field || ! in_array( $operator, CSMF_Condition_Engine::operators(), true ) ) {
				continue;
			}
			$clean[] = array(
				'field'    => $field,
				'operator' => $operator,
				'value'    => self::plain( $condition['value'] ?? '', 500 ),
			);
		}
		return $clean;
	}

	private static function field_name( $value ) {
		$value = preg_replace( '/[^a-zA-Z0-9_\-\[\]]/', '', (string) $value );
		return substr( $value, 0, 190 );
	}

	private static function rule_id( $value ) {
		$value = sanitize_key( $value );
		return $value ?: wp_generate_uuid4();
	}

	private static function plain( $value, $length ) {
		$value = is_scalar( $value ) ? wp_unslash( (string) $value ) : '';
		$value = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value );
		return function_exists( 'mb_substr' ) ? mb_substr( trim( $value ), 0, $length ) : substr( trim( $value ), 0, $length );
	}

	private static function email_templates( $value ) {
		$value = self::plain( $value, 1000 );
		return preg_replace( '/[\r\n]+/', '', $value );
	}

	private static function array_value( array $array, $key ) {
		return isset( $array[ $key ] ) && is_array( $array[ $key ] ) ? $array[ $key ] : array();
	}

	/**
	 * Read the 1.x meta schema without mutating it. Saving in the new admin screen
	 * writes the normalized 2.x schema, so existing sites upgrade safely.
	 *
	 * @param int $form_id Form post ID.
	 * @return array<string,mixed>
	 */
	private static function legacy_config( $form_id ) {
		$config      = self::defaults();
		$has_legacy  = false;
		$global      = get_option( 'csmf_settings', array() );
		$validations = get_post_meta( $form_id, '_csmf_validation_rules', true );
		$conditions  = get_post_meta( $form_id, '_csmf_conditional_rules', true );
		$recipients  = get_post_meta( $form_id, '_csmf_mail_routing_rules', true );

		if ( is_array( $global ) ) {
			$config['realtime']    = ! isset( $global['enable_realtime_validation'] ) || ! empty( $global['enable_realtime_validation'] );
			$config['validate_on'] = in_array( $global['validation_timing'] ?? '', array( 'blur', 'input', 'blur_input' ), true ) ? $global['validation_timing'] : 'blur_input';
		}

		$type_map = array( 'pattern' => 'regex', 'match' => 'equals_field', 'postal_code_jp' => 'postal_jp' );
		if ( is_array( $validations ) ) {
			foreach ( $validations as $field => $rules ) {
				if ( ! is_array( $rules ) ) { continue; }
				foreach ( $rules as $type => $value ) {
					$type = $type_map[ $type ] ?? $type;
					if ( ! in_array( $type, self::validation_types(), true ) || false === $value || '' === $value ) { continue; }
					$config['validations'][] = array( 'id' => wp_generate_uuid4(), 'enabled' => true, 'field' => $field, 'type' => $type, 'param' => true === $value ? '' : $value, 'message' => '' );
					$has_legacy = true;
				}
			}
		}

		$operator_map = array( 'is_empty' => 'empty', 'is_not_empty' => 'not_empty' );
		if ( is_array( $conditions ) ) {
			foreach ( $conditions as $rule ) {
				if ( empty( $rule['trigger'] ) || empty( $rule['target'] ) ) { continue; }
				$config['field_rules'][] = array(
					'id' => wp_generate_uuid4(), 'enabled' => true, 'target' => $rule['target'],
					'action' => 'hide' === ( $rule['action'] ?? '' ) ? 'hide_when' : 'show_when', 'scope' => 'item', 'relation' => 'all',
					'conditions' => array( array( 'field' => $rule['trigger'], 'operator' => $operator_map[ $rule['operator'] ?? '' ] ?? ( $rule['operator'] ?? 'equals' ), 'value' => $rule['value'] ?? '' ) ),
				);
				$has_legacy = true;
			}
		}

		if ( is_array( $recipients ) ) {
			foreach ( $recipients as $rule ) {
				if ( empty( $rule['field'] ) || empty( $rule['email_to'] ) ) { continue; }
				$config['recipient_rules'][] = array(
					'id' => wp_generate_uuid4(), 'enabled' => true, 'label' => '', 'priority' => 10, 'relation' => 'all',
					'conditions' => array( array( 'field' => $rule['field'], 'operator' => $operator_map[ $rule['operator'] ?? '' ] ?? ( $rule['operator'] ?? 'equals' ), 'value' => $rule['value'] ?? '' ) ),
					'to' => $rule['email_to'], 'cc' => '', 'bcc' => '', 'reply_to' => '', 'subject_prefix' => '',
				);
				$has_legacy = true;
			}
		}

		return $has_legacy ? $config : array();
	}
}
