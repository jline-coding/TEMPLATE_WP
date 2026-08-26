<?php
/**
 * Shared server-side condition evaluator.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_Condition_Engine {
	/** @return string[] */
	public static function operators() {
		return array( 'equals', 'not_equals', 'contains', 'not_contains', 'in', 'not_in', 'empty', 'not_empty', 'greater', 'greater_equal', 'less', 'less_equal', 'regex', 'checked', 'unchecked' );
	}

	/**
	 * Evaluate a list of conditions.
	 *
	 * Empty lists deliberately do not match, preventing catch-all routes by mistake.
	 *
	 * @param array<int,array<string,string>> $conditions Conditions.
	 * @param string                          $relation   all|any.
	 * @param array<string,mixed>             $data       Submitted values.
	 * @return bool
	 */
	public static function evaluate_group( array $conditions, $relation, array $data ) {
		if ( ! $conditions ) {
			return false;
		}

		$results = array();
		foreach ( $conditions as $condition ) {
			$field     = $condition['field'] ?? '';
			$submitted = array_key_exists( $field, $data ) ? $data[ $field ] : null;
			$results[] = self::evaluate( $submitted, $condition['operator'] ?? '', $condition['value'] ?? '' );
		}

		return 'any' === $relation ? in_array( true, $results, true ) : ! in_array( false, $results, true );
	}

	/** @return bool */
	public static function evaluate( $actual, $operator, $expected = '' ) {
		$values    = is_array( $actual ) ? array_map( array( __CLASS__, 'string_value' ), $actual ) : array( self::string_value( $actual ) );
		$actual_s  = implode( ',', $values );
		$expected  = self::string_value( $expected );
		$is_empty  = '' === trim( $actual_s );

		switch ( $operator ) {
			case 'equals':
				return in_array( $expected, $values, true );
			case 'not_equals':
				return ! in_array( $expected, $values, true );
			case 'contains':
				return false !== self::strpos( $actual_s, $expected );
			case 'not_contains':
				return false === self::strpos( $actual_s, $expected );
			case 'in':
			case 'not_in':
				$options = array_map( 'trim', preg_split( '/[\r\n,]+/', $expected ) );
				$match   = (bool) array_intersect( $values, $options );
				return 'in' === $operator ? $match : ! $match;
			case 'empty':
			case 'unchecked':
				return $is_empty;
			case 'not_empty':
			case 'checked':
				return ! $is_empty;
			case 'greater':
				return is_numeric( $actual_s ) && (float) $actual_s > (float) $expected;
			case 'greater_equal':
				return is_numeric( $actual_s ) && (float) $actual_s >= (float) $expected;
			case 'less':
				return is_numeric( $actual_s ) && (float) $actual_s < (float) $expected;
			case 'less_equal':
				return is_numeric( $actual_s ) && (float) $actual_s <= (float) $expected;
			case 'regex':
				return self::safe_regex( $expected, $actual_s );
			default:
				return false;
		}
	}

	/** @return bool */
	private static function safe_regex( $pattern, $subject ) {
		if ( '' === $pattern || strlen( $pattern ) > 500 ) {
			return false;
		}
		$delimiter = '~';
		$pattern   = $delimiter . str_replace( $delimiter, '\\' . $delimiter, $pattern ) . $delimiter . 'u';
		return 1 === @preg_match( $pattern, $subject ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	/** @return string */
	private static function string_value( $value ) {
		if ( is_bool( $value ) ) {
			return $value ? '1' : '';
		}
		return is_scalar( $value ) ? trim( (string) $value ) : '';
	}

	/** @return int|false */
	private static function strpos( $haystack, $needle ) {
		return function_exists( 'mb_strpos' ) ? mb_strpos( $haystack, $needle ) : strpos( $haystack, $needle );
	}
}
