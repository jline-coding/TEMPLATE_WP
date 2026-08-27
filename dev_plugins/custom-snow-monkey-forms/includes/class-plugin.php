<?php
/**
 * Plugin bootstrap.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_Plugin {
	/** @var CSMF_Plugin|null */
	private static $instance;

	/** @return CSMF_Plugin */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'boot' ), 20 );
	}

	/** Initialize after Snow Monkey Forms has loaded. */
	public function boot() {
		load_plugin_textdomain( 'custom-snow-monkey-forms', false, dirname( plugin_basename( CSMF_FILE ) ) . '/languages' );

		if ( ! $this->dependency_available() ) {
			add_action( 'admin_notices', array( $this, 'dependency_notice' ) );
			return;
		}

		new CSMF_Integration();
		new CSMF_REST_API();
		if ( is_admin() ) {
			new CSMF_Admin();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ), 30 );
	}

	/** @return bool */
	public function dependency_available() {
		return defined( 'SNOW_MONKEY_FORMS_PATH' ) && class_exists( '\\Snow_Monkey\\Plugin\\Forms\\App\\Model\\Setting' );
	}

	/** Show dependency warning. */
	public function dependency_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p><strong>Custom Snow Monkey Forms:</strong> Snow Monkey Forms を有効化してください。</p></div>';
	}

	/** Enqueue the small runtime. */
	public function enqueue_frontend() {
		$script    = CSMF_PATH . 'assets/js/frontend.js';
		$yubinbango = CSMF_PATH . 'assets/js/yubinbango.js';
		$style     = CSMF_PATH . 'assets/css/frontend.css';

		wp_enqueue_style( 'custom-snow-monkey-forms', CSMF_URL . 'assets/css/frontend.css', array( 'snow-monkey-forms' ), file_exists( $style ) ? filemtime( $style ) : CSMF_VERSION );

		if ( file_exists( $yubinbango ) ) {
			wp_enqueue_script( 'yubinbango', CSMF_URL . 'assets/js/yubinbango.js', array(), filemtime( $yubinbango ), true );
		}

		$dependencies = file_exists( $yubinbango ) ? array( 'snow-monkey-forms', 'yubinbango' ) : array( 'snow-monkey-forms' );
		wp_enqueue_script( 'custom-snow-monkey-forms', CSMF_URL . 'assets/js/frontend.js', $dependencies, file_exists( $script ) ? filemtime( $script ) : CSMF_VERSION, true );
		$is_vi = 'vi' === CSMF_I18n::get_language();
		wp_localize_script(
			'custom-snow-monkey-forms',
			'csmfRuntime',
			array(
				'endpoint' => esc_url_raw( rest_url( 'custom-snow-monkey-forms/v1/config/' ) ),
				'i18n'     => array(
					'configurationError' => $is_vi ? 'Không thể tải cấu hình biểu mẫu.' : 'フォーム設定を読み込めませんでした。',
					'fixErrors'          => $is_vi ? 'Vui lòng kiểm tra lại nội dung đã nhập.' : '入力内容をご確認ください。',
					'checkingImage'      => $is_vi ? 'Đang kiểm tra tệp/ảnh. Vui lòng đợi...' : '画像・ファイルを確認しています。しばらくお待ちください。',
					'fileInvalidType'    => $is_vi ? 'Định dạng tệp không được phép (%s).' : '許可されたファイル形式（%s）を選択してください。',
					'fileMaxSize'        => $is_vi ? 'Dung lượng tệp không được vượt quá %sMB.' : 'ファイルサイズは %sMB 以下にしてください。',
					'imageInvalid'       => $is_vi ? 'Tệp ảnh không hợp lệ hoặc bị hỏng.' : '画像ファイルを読み込めません。',
					'imageMinWidth'      => $is_vi ? 'Chiều rộng ảnh phải tối thiểu %dpx.' : '画像の横幅は %dpx 以上にしてください。',
					'imageMaxWidth'      => $is_vi ? 'Chiều rộng ảnh tối đa là %dpx.' : '画像の横幅は %dpx 以下にしてください。',
					'imageMinHeight'     => $is_vi ? 'Chiều cao ảnh phải tối thiểu %dpx.' : '画像の高さは %dpx 以上にしてください。',
					'imageMaxHeight'     => $is_vi ? 'Chiều cao ảnh tối đa là %dpx.' : '画像の高さは %dpx 以下にしてください。',
				),
			)
		);
	}

	/** Activation compatibility guard. */
	public static function activate() {
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			deactivate_plugins( plugin_basename( CSMF_FILE ) );
			wp_die( esc_html__( 'Custom Snow Monkey Forms requires PHP 7.4 or later.', 'custom-snow-monkey-forms' ) );
		}
	}
}
