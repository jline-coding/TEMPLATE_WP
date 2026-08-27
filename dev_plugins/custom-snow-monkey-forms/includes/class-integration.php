<?php
/**
 * Snow Monkey Forms integration layer.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_Integration {
	/** @var array<int,array<string,bool>> */
	private $hidden_cache = array();

	public function __construct() {
		add_filter( 'snow_monkey_forms/form/attributes', array( $this, 'filter_form_attributes' ), 20, 2 );
		add_filter( 'snow_monkey_forms/control/attributes', array( $this, 'filter_control_attributes' ), 20, 2 );
		add_filter( 'snow_monkey_forms/spam/validate', array( $this, 'validate_submission' ), 20, 3 );
		add_filter( 'snow_monkey_forms/administrator_mailer/args', array( $this, 'administrator_mail_args' ), 20, 3 );
		add_filter( 'snow_monkey_forms/auto_reply_mailer/args', array( $this, 'auto_reply_mail_args' ), 20, 3 );
	}

	/**
	 * Automatically add h-adr class to the form if postal autofill is enabled.
	 *
	 * @param array $attributes Form attributes.
	 * @param object $setting   Snow Monkey Forms Setting.
	 * @return array
	 */
	public function filter_form_attributes( $attributes, $setting ) {
		$form_id = absint( $setting->get( 'form_id' ) );
		$config  = CSMF_Config::get( $form_id );
		if ( ! empty( $config['enabled'] ) && ! empty( $config['postal_autofill']['enabled'] ) ) {
			$class = isset( $attributes['class'] ) ? (string) $attributes['class'] : '';
			if ( false === strpos( $class, 'h-adr' ) ) {
				$attributes['class'] = trim( $class . ' h-adr' );
			}
		}
		return $attributes;
	}

	/**
	 * Make core required/file validation agree with conditional visibility and postal auto-fill.
	 *
	 * @param array $attributes Control attributes.
	 * @param object $setting   Snow Monkey Forms Setting.
	 * @return array
	 */
	public function filter_control_attributes( $attributes, $setting ) {
		$form_id = absint( $setting->get( 'form_id' ) );
		$config  = CSMF_Config::get( $form_id );
		$name    = isset( $attributes['name'] ) ? (string) $attributes['name'] : '';
		if ( ! $config['enabled'] || ! $name ) {
			return $attributes;
		}

		$hidden = $this->hidden_fields_from_request( $form_id, $config );
		if ( ! empty( $hidden[ $name ] ) ) {
			$attributes['validations'] = array();
		}

		foreach ( $config['uploads'] as $upload ) {
			if ( $upload['enabled'] && $name === $upload['field'] ) {
				$attributes['validations']             = isset( $attributes['validations'] ) && is_array( $attributes['validations'] ) ? $attributes['validations'] : array();
				$attributes['validations']['uploaded'] = empty( $hidden[ $name ] ) && $upload['required'];
				break;
			}
		}

		// Attach YubinBango Microformats classes
		if ( ! empty( $config['postal_autofill']['enabled'] ) ) {
			$postal = $config['postal_autofill'];
			$add_class = '';
			if ( $postal['postal_field'] && $name === $postal['postal_field'] ) {
				$add_class = 'p-postal-code';
			} elseif ( $postal['region_field'] && $name === $postal['region_field'] ) {
				$add_class = 'p-region';
			} elseif ( $postal['locality_field'] && $name === $postal['locality_field'] ) {
				$add_class = 'p-locality';
			} elseif ( $postal['street_field'] && $name === $postal['street_field'] ) {
				$add_class = 'p-street-address';
			}

			if ( $add_class ) {
				$current_class = isset( $attributes['controlClass'] ) ? (string) $attributes['controlClass'] : ( isset( $attributes['class'] ) ? (string) $attributes['class'] : '' );
				if ( false === strpos( $current_class, $add_class ) ) {
					$attributes['controlClass'] = trim( $current_class . ' ' . $add_class );
					$attributes['class']        = $attributes['controlClass'];
				}
			}
		}

		return $attributes;
	}

	/**
	 * Server-side validation is mandatory because browser checks can be bypassed.
	 *
	 * @param bool|WP_Error $valid     Previous validation result.
	 * @param object        $responser Snow Monkey Forms Responser.
	 * @param object        $setting   Snow Monkey Forms Setting.
	 * @return bool|WP_Error
	 */
	public function validate_submission( $valid, $responser, $setting ) {
		if ( is_wp_error( $valid ) || ! $valid ) {
			return $valid;
		}
		$form_id = absint( $setting->get( 'form_id' ) );
		$config  = CSMF_Config::get( $form_id );
		if ( ! $config['enabled'] ) {
			return $valid;
		}

		$data   = $responser->get_all();
		$hidden = $this->hidden_fields( $config, $data );
		foreach ( array_keys( $hidden ) as $name ) {
			if ( $config['clear_hidden'] ) {
				$responser->update( $name, '' );
				$data[ $name ] = '';
			}
		}

		$errors = $this->validate_values( $config, $data, $hidden );
		$errors = array_merge( $errors, $this->validate_files( $config, $data, $setting, $hidden ) );
		$errors = apply_filters( 'csmf/validation/errors', $errors, $data, $config, $responser, $setting );
		if ( $errors ) {
			return new WP_Error( 'csmf_validation_failed', implode( "\n", array_unique( $errors ) ) );
		}
		return $valid;
	}

	/** @return array */
	public function administrator_mail_args( $args, $responser, $setting ) {
		return $this->mail_args( $args, $responser, $setting, 'admin' );
	}

	/** @return array */
	public function auto_reply_mail_args( $args, $responser, $setting ) {
		return $this->mail_args( $args, $responser, $setting, 'reply' );
	}

	/** @return array */
	private function mail_args( $args, $responser, $setting, $context ) {
		$form_id = absint( $setting->get( 'form_id' ) );
		$config  = CSMF_Config::get( $form_id );
		if ( ! $config['enabled'] ) {
			return $args;
		}

		$data = $responser->get_all();
		if ( 'admin' === $context ) {
			$rules = array_filter( $config['recipient_rules'], function ( $rule ) { return $rule['enabled']; } );
			usort( $rules, function ( $a, $b ) { return $a['priority'] <=> $b['priority']; } );
			$matches = array();
			foreach ( $rules as $rule ) {
				if ( CSMF_Condition_Engine::evaluate_group( $rule['conditions'], $rule['relation'], $data ) ) {
					$matches[] = $rule;
					if ( 'first_match' === $config['route_mode'] ) {
						break;
					}
				}
			}

			if ( $matches ) {
				$to = array();
				foreach ( $matches as $rule ) {
					$to = array_merge( $to, $this->resolve_emails( $rule['to'], $data ) );
					$args['headers'] = $this->append_headers( $args['headers'] ?? array(), 'Cc', $this->resolve_emails( $rule['cc'], $data ) );
					$args['headers'] = $this->append_headers( $args['headers'] ?? array(), 'Bcc', $this->resolve_emails( $rule['bcc'], $data ) );
					$reply_to = $this->resolve_emails( $rule['reply_to'], $data );
					if ( $reply_to ) {
						$args['replyto'] = reset( $reply_to );
					}
					if ( $rule['subject_prefix'] ) {
						$args['subject'] = $this->expand_text( $rule['subject_prefix'], $data ) . ( $args['subject'] ?? '' );
					}
				}
				if ( $to ) {
					$args['to'] = implode( ',', array_unique( $to ) );
				}
			}
		}

		$args['attachments'] = $this->configured_attachments( $args['attachments'] ?? array(), $config, $data, $setting, $context );
		return apply_filters( 'csmf/mail/args', $args, $context, $data, $config, $responser, $setting );
	}

	/** @return array<string,bool> */
	private function hidden_fields_from_request( $form_id, array $config ) {
		if ( isset( $this->hidden_cache[ $form_id ] ) ) {
			return $this->hidden_cache[ $form_id ];
		}
		// Snow Monkey Forms has already verified the request before this data is used.
		$data = isset( $_POST ) && is_array( $_POST ) ? wp_unslash( $_POST ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$this->hidden_cache[ $form_id ] = $this->hidden_fields( $config, $data );
		return $this->hidden_cache[ $form_id ];
	}

	/** @return array<string,bool> */
	private function hidden_fields( array $config, array $data ) {
		$states = array();
		foreach ( $config['field_rules'] as $rule ) {
			if ( ! $rule['enabled'] ) {
				continue;
			}
			$matched = CSMF_Condition_Engine::evaluate_group( $rule['conditions'], $rule['relation'], $data );
			$visible = 'show_when' === $rule['action'] ? $matched : ! $matched;
			if ( ! isset( $states[ $rule['target'] ] ) ) {
				$states[ $rule['target'] ] = true;
			}
			$states[ $rule['target'] ] = $states[ $rule['target'] ] && $visible;
		}
		$hidden = array_filter( $states, function ( $visible ) { return ! $visible; } );
		return apply_filters( 'csmf/conditional/hidden_fields', $hidden, $data, $config );
	}

	/** @return string[] */
	private function validate_values( array $config, array $data, array $hidden ) {
		$errors = array();
		foreach ( $config['validations'] as $rule ) {
			if ( ! $rule['enabled'] || ! empty( $hidden[ $rule['field'] ] ) ) {
				continue;
			}
			$value = $data[ $rule['field'] ] ?? '';
			if ( $this->validation_passes( $rule['type'], $value, $rule['param'], $data ) ) {
				continue;
			}
			$errors[] = $rule['message'] ?: $this->default_message( $rule['type'], $rule['param'] );
		}
		return $errors;
	}

	/** @return bool */
	private function validation_passes( $type, $value, $param, array $data ) {
		$value = is_array( $value ) ? implode( ',', $value ) : trim( (string) $value );
		if ( 'required' !== $type && '' === $value ) {
			return true;
		}
		$length = function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
		switch ( $type ) {
			case 'required': return '' !== $value;
			case 'email': return (bool) is_email( $value );
			case 'tel_jp': return (bool) preg_match( '/^(?:\+81|0)[0-9\-() ]{8,18}$/', $value );
			case 'postal_jp': return (bool) preg_match( '/^\d{3}-?\d{4}$/', $value );
			case 'hiragana': return (bool) preg_match( '/^[ぁ-ゖー\s　]+$/u', $value );
			case 'katakana': return (bool) preg_match( '/^[ァ-ヺー\s　]+$/u', $value );
			case 'url': return (bool) wp_http_validate_url( $value );
			case 'numeric': return is_numeric( $value );
			case 'min_length': return $length >= absint( $param );
			case 'max_length': return $length <= absint( $param );
			case 'min': return is_numeric( $value ) && (float) $value >= (float) $param;
			case 'max': return is_numeric( $value ) && (float) $value <= (float) $param;
			case 'regex': return CSMF_Condition_Engine::evaluate( $value, 'regex', $param );
			case 'equals_field': return $value === (string) ( $data[ $param ] ?? '' );
			case 'different_field': return $value !== (string) ( $data[ $param ] ?? '' );
			default: return true;
		}
	}

	/** @return string */
	private function default_message( $type, $param ) {
		$lang = CSMF_I18n::get_language();
		if ( 'vi' === $lang ) {
			$messages = array(
				'required'        => 'Vui lòng nhập trường bắt buộc này.',
				'email'           => 'Vui lòng nhập địa chỉ email hợp lệ.',
				'tel_jp'          => 'Vui lòng nhập số điện thoại hợp lệ.',
				'postal_jp'       => 'Vui lòng nhập mã bưu điện hợp lệ (Ví dụ: 100-0001).',
				'hiragana'        => 'Vui lòng chỉ nhập ký tự Hiragana.',
				'katakana'        => 'Vui lòng chỉ nhập ký tự Katakana.',
				'url'             => 'Vui lòng nhập đường dẫn URL hợp lệ.',
				'numeric'         => 'Vui lòng chỉ nhập số.',
				'min_length'      => 'Vui lòng nhập tối thiểu ' . absint( $param ) . ' ký tự.',
				'max_length'      => 'Vui lòng nhập tối đa ' . absint( $param ) . ' ký tự.',
				'min'             => 'Giá trị phải lớn hơn hoặc bằng ' . $param . '.',
				'max'             => 'Giá trị phải nhỏ hơn hoặc bằng ' . $param . '.',
				'regex'           => 'Định dạng dữ liệu không hợp lệ.',
				'equals_field'    => 'Giá trị không khớp với trường yêu cầu.',
				'different_field' => 'Giá trị không được trùng với trường yêu cầu.',
			);
			return $messages[ $type ] ?? 'Vui lòng kiểm tra lại nội dung đã nhập.';
		}

		$messages = array(
			'required' => '必須項目を入力してください。', 'email' => '有効なメールアドレスを入力してください。',
			'tel_jp' => '有効な電話番号を入力してください。', 'postal_jp' => '郵便番号を正しく入力してください（例：100-0001）。',
			'hiragana' => 'ひらがなで入力してください。', 'katakana' => 'カタカナで入力してください。',
			'url' => '有効なURLを入力してください。', 'numeric' => '数値で入力してください。',
			'min_length' => absint( $param ) . '文字以上で入力してください。', 'max_length' => absint( $param ) . '文字以内で入力してください。',
			'min' => $param . '以上の値を入力してください。', 'max' => $param . '以下の値を入力してください。',
			'regex' => '入力形式が正しくありません。', 'equals_field' => '入力内容が一致していません。', 'different_field' => '異なる内容を入力してください。',
		);
		return $messages[ $type ] ?? '入力内容を確認してください。';
	}

	/** @return string[] */
	private function validate_files( array $config, array $data, $setting, array $hidden ) {
		$errors   = array();
		$lang     = CSMF_I18n::get_language();
		$is_vi    = 'vi' === $lang;
		$all_mimes = CSMF_Config::allowed_mimes();
		$img_mimes = CSMF_Config::image_mimes();

		foreach ( $config['uploads'] as $rule ) {
			if ( ! $rule['enabled'] || ! empty( $hidden[ $rule['field'] ] ) ) {
				continue;
			}
			$file = isset( $_FILES[ $rule['field'] ] ) && is_array( $_FILES[ $rule['field'] ] ) ? $_FILES[ $rule['field'] ] : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$path = $file && UPLOAD_ERR_OK === (int) ( $file['error'] ?? -1 ) ? (string) $file['tmp_name'] : '';
			$name = $file ? sanitize_file_name( wp_unslash( $file['name'] ?? '' ) ) : sanitize_file_name( basename( (string) ( $data[ $rule['field'] ] ?? '' ) ) );
			if ( ! $path && $name ) {
				$directory_class = '\\Snow_Monkey\\Plugin\\Forms\\App\\Model\\Directory';
				$candidate = $directory_class::generate_user_filepath( $rule['field'], $name );
				$path = is_file( $candidate ) ? $candidate : '';
			}
			if ( ! $path ) {
				if ( $rule['required'] ) {
					$errors[] = $is_vi ? 'Vui lòng chọn tệp tin đính kèm.' : 'ファイルを選択してください。';
				}
				continue;
			}

			$extension = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
			$allowed   = array_filter( array_map( 'trim', explode( ',', strtolower( $rule['extensions'] ) ) ) );

			// Check allowed extension
			if ( ! in_array( $extension, $allowed, true ) || empty( $all_mimes[ $extension ] ) ) {
				$errors[] = $is_vi
					? sprintf( 'Định dạng tệp không được phép (%s).', implode( ' / ', $allowed ) )
					: sprintf( '許可されたファイル形式（%s）を選択してください。', implode( ' / ', $allowed ) );
				continue;
			}

			// Check file size
			$max_bytes = (float) $rule['max_mb'] * 1024 * 1024;
			if ( filesize( $path ) > $max_bytes ) {
				$errors[] = $is_vi
					? sprintf( 'Dung lượng tệp không được vượt quá %sMB.', $rule['max_mb'] )
					: sprintf( 'ファイルサイズは %sMB 以下にしてください。', $rule['max_mb'] );
			}

			// If it is an image format, validate image headers and dimensions
			if ( array_key_exists( $extension, $img_mimes ) ) {
				$image = @getimagesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( ! $image || $img_mimes[ $extension ] !== ( $image['mime'] ?? '' ) ) {
					$errors[] = $is_vi ? 'Tệp ảnh không hợp lệ hoặc bị hỏng.' : '画像ファイルが正しくありません。';
					continue;
				}
				if ( $rule['min_width'] && $image[0] < $rule['min_width'] ) {
					$errors[] = $is_vi
						? sprintf( 'Chiều rộng ảnh phải tối thiểu %dpx.', $rule['min_width'] )
						: sprintf( '画像の横幅は %dpx 以上にしてください。', $rule['min_width'] );
				}
				if ( $rule['max_width'] && $image[0] > $rule['max_width'] ) {
					$errors[] = $is_vi
						? sprintf( 'Chiều rộng ảnh tối đa là %dpx.', $rule['max_width'] )
						: sprintf( '画像の横幅は %dpx 以下にしてください。', $rule['max_width'] );
				}
				if ( $rule['min_height'] && $image[1] < $rule['min_height'] ) {
					$errors[] = $is_vi
						? sprintf( 'Chiều cao ảnh phải tối thiểu %dpx.', $rule['min_height'] )
						: sprintf( '画像の高さは %dpx 以上にしてください。', $rule['min_height'] );
				}
				if ( $rule['max_height'] && $image[1] > $rule['max_height'] ) {
					$errors[] = $is_vi
						? sprintf( 'Chiều cao ảnh tối đa là %dpx.', $rule['max_height'] )
						: sprintf( '画像の高さは %dpx 以下にしてください。', $rule['max_height'] );
				}
			}
		}
		return $errors;
	}

	/** @return array */
	private function configured_attachments( $attachments, array $config, array $data, $setting, $context ) {
		$attachments = is_array( $attachments ) ? $attachments : array_filter( array( $attachments ) );
		$directory_class = '\\Snow_Monkey\\Plugin\\Forms\\App\\Model\\Directory';
		foreach ( $config['uploads'] as $rule ) {
			$attach = 'admin' === $context ? $rule['attach_admin'] : $rule['attach_reply'];
			$name   = sanitize_file_name( basename( (string) ( $data[ $rule['field'] ] ?? '' ) ) );
			if ( ! $rule['enabled'] || ! $attach || ! $name ) { continue; }
			$path = $directory_class::generate_user_filepath( $rule['field'], $name );
			if ( is_file( $path ) ) { $attachments[] = $path; }
		}
		$attachments = array_values( array_unique( $attachments ) );
		return apply_filters( 'csmf/mail/attachments', $attachments, $context, $config, $data, $setting );
	}

	/** @return string[] */
	private function resolve_emails( $template, array $data ) {
		$expanded = preg_replace_callback( '/{([a-zA-Z0-9_\-\[\]]+)}/', function ( $match ) use ( $data ) {
			$value = $data[ $match[1] ] ?? '';
			return is_scalar( $value ) ? (string) $value : '';
		}, (string) $template );
		$emails = preg_split( '/[,;]+/', str_replace( array( "\r", "\n" ), '', $expanded ) );
		return array_values( array_unique( array_filter( array_map( 'sanitize_email', $emails ), 'is_email' ) ) );
	}

	/** @return array */
	private function append_headers( $headers, $name, array $emails ) {
		$headers = is_array( $headers ) ? $headers : preg_split( '/\r?\n/', (string) $headers );
		if ( $emails ) { $headers[] = $name . ': ' . implode( ', ', $emails ); }
		return array_filter( $headers );
	}

	/** @return string */
	private function expand_text( $template, array $data ) {
		return preg_replace_callback( '/{([a-zA-Z0-9_\-\[\]]+)}/', function ( $match ) use ( $data ) {
			$value = $data[ $match[1] ] ?? '';
			$value = is_array( $value ) ? implode( ', ', $value ) : (string) $value;
			return sanitize_text_field( $value );
		}, $template );
	}
}
