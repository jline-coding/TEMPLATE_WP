<?php
/**
 * Bilingual Japanese/Vietnamese administration screen.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_Admin {
	const PAGE_SLUG = 'csmf-settings';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_csmf_save_config', array( $this, 'save' ) );
		add_action( 'admin_post_csmf_set_admin_language', array( $this, 'set_admin_language' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( CSMF_FILE ), array( $this, 'action_links' ) );
	}

	public function add_menu() {
		add_submenu_page(
			'edit.php?post_type=snow-monkey-forms',
			CSMF_I18n::t( 'menu_title' ),
			CSMF_I18n::t( 'menu_title' ),
			'edit_posts',
			self::PAGE_SLUG,
			array( $this, 'render' )
		);
	}

	/** @return array */
	public function action_links( $links ) {
		$url = admin_url( 'edit.php?post_type=snow-monkey-forms&page=' . self::PAGE_SLUG );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html( CSMF_I18n::t( 'settings' ) ) . '</a>' );
		return $links;
	}

	public function enqueue_assets( $hook ) {
		if ( 'snow-monkey-forms_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		$form_id = $this->requested_form_id();
		$script  = CSMF_PATH . 'assets/js/admin.js';
		$style   = CSMF_PATH . 'assets/css/admin.css';
		wp_enqueue_style( 'csmf-admin', CSMF_URL . 'assets/css/admin.css', array(), file_exists( $style ) ? filemtime( $style ) : CSMF_VERSION );
		wp_enqueue_script( 'csmf-admin', CSMF_URL . 'assets/js/admin.js', array(), file_exists( $script ) ? filemtime( $script ) : CSMF_VERSION, true );
		wp_localize_script(
			'csmf-admin',
			'csmfAdmin',
			array(
				'config' => $form_id ? CSMF_Config::get( $form_id ) : CSMF_Config::defaults(),
				'fields' => $form_id ? $this->form_fields( $form_id ) : array(),
				'language'  => CSMF_I18n::get_language(),
				'i18n'      => CSMF_I18n::strings(),
				'types'     => CSMF_I18n::validation_types(),
				'operators' => CSMF_I18n::operators(),
			)
		);
	}

	public function render() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html( CSMF_I18n::t( 'no_permission' ) ) );
		}
		$strings = CSMF_I18n::strings();
		$forms   = get_posts( array( 'post_type' => 'snow-monkey-forms', 'post_status' => array( 'publish', 'draft', 'private' ), 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		$form_id = $this->requested_form_id();
		if ( ! $form_id && $forms ) {
			$form_id = (int) $forms[0]->ID;
		}
		?>
		<div class="wrap csmf-admin">
			<h1><?php echo esc_html( $strings['page_title'] ); ?></h1>
			<p class="description"><?php echo esc_html( $strings['page_description'] ); ?></p>

			<div class="csmf-toolbar">
				<label for="csmf-form-selector"><strong><?php echo esc_html( $strings['target_form'] ); ?></strong></label>
				<select id="csmf-form-selector" data-base-url="<?php echo esc_url( admin_url( 'edit.php?post_type=snow-monkey-forms&page=' . self::PAGE_SLUG ) ); ?>">
					<?php foreach ( $forms as $form ) : ?>
						<option value="<?php echo esc_attr( $form->ID ); ?>" <?php selected( $form_id, $form->ID ); ?>><?php echo esc_html( $form->post_title ?: '(' . $strings['untitled'] . ' #' . $form->ID . ')' ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( $form_id ) : ?>
					<a class="button" href="<?php echo esc_url( get_edit_post_link( $form_id ) ); ?>"><?php echo esc_html( $strings['edit_form'] ); ?></a>
				<?php endif; ?>
				<form class="csmf-language-switcher" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="csmf_set_admin_language">
					<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
					<?php wp_nonce_field( 'csmf_set_admin_language', 'csmf_language_nonce' ); ?>
					<label for="csmf-admin-language"><strong><?php echo esc_html( $strings['language'] ); ?></strong></label>
					<select id="csmf-admin-language" name="language">
						<option value="ja" <?php selected( CSMF_I18n::get_language(), 'ja' ); ?>><?php echo esc_html( $strings['language_ja'] ); ?></option>
						<option value="vi" <?php selected( CSMF_I18n::get_language(), 'vi' ); ?>><?php echo esc_html( $strings['language_vi'] ); ?></option>
					</select>
					<noscript><button type="submit" class="button"><?php echo esc_html( $strings['settings'] ); ?></button></noscript>
				</form>
			</div>

			<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $strings['settings_saved'] ); ?></p></div>
			<?php endif; ?>

			<?php if ( ! $forms ) : ?>
				<div class="notice notice-warning"><p><?php echo esc_html( $strings['no_forms'] ); ?></p></div>
			<?php elseif ( $form_id ) : ?>
				<form id="csmf-settings-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="csmf_save_config">
					<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
					<input type="hidden" id="csmf-config-json" name="config_json" value="">
					<?php wp_nonce_field( 'csmf_save_' . $form_id, 'csmf_nonce' ); ?>

					<nav class="nav-tab-wrapper" aria-label="<?php echo esc_attr( $strings['nav_label'] ); ?>">
						<button type="button" class="nav-tab nav-tab-active" data-tab="general"><?php echo esc_html( $strings['tab_general'] ); ?></button>
						<button type="button" class="nav-tab" data-tab="validation"><?php echo esc_html( $strings['tab_validation'] ); ?></button>
						<button type="button" class="nav-tab" data-tab="conditions"><?php echo esc_html( $strings['tab_conditions'] ); ?></button>
						<button type="button" class="nav-tab" data-tab="recipients"><?php echo esc_html( $strings['tab_recipients'] ); ?></button>
						<button type="button" class="nav-tab" data-tab="uploads"><?php echo esc_html( $strings['tab_uploads'] ); ?></button>
						<button type="button" class="nav-tab" data-tab="diagnostics"><?php echo esc_html( $strings['tab_diagnostics'] ); ?></button>
					</nav>

					<div id="csmf-admin-app" class="csmf-app" aria-live="polite"></div>
					<noscript><div class="notice notice-error"><p><?php echo esc_html( $strings['javascript_required'] ); ?></p></div></noscript>
					<div class="csmf-savebar"><button type="submit" class="button button-primary button-hero"><?php echo esc_html( $strings['save_changes'] ); ?></button><span class="csmf-save-status" role="status"></span></div>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	public function save() {
		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		if ( ! $form_id || ! current_user_can( 'edit_post', $form_id ) ) {
			wp_die( esc_html( CSMF_I18n::t( 'no_edit_permission' ) ) );
		}
		check_admin_referer( 'csmf_save_' . $form_id, 'csmf_nonce' );
		$json = isset( $_POST['config_json'] ) ? wp_unslash( $_POST['config_json'] ) : '{}';
		$config = json_decode( $json, true );
		if ( ! is_array( $config ) || JSON_ERROR_NONE !== json_last_error() ) {
			wp_die( esc_html( CSMF_I18n::t( 'invalid_config' ) ) );
		}
		CSMF_Config::save( $form_id, $config );
		wp_safe_redirect( add_query_arg( array( 'post_type' => 'snow-monkey-forms', 'page' => self::PAGE_SLUG, 'form_id' => $form_id, 'updated' => 1 ), admin_url( 'edit.php' ) ) );
		exit;
	}

	/** Save the admin language for the current WordPress user. */
	public function set_admin_language() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html( CSMF_I18n::t( 'no_permission' ) ) );
		}
		check_admin_referer( 'csmf_set_admin_language', 'csmf_language_nonce' );
		$language = isset( $_POST['language'] ) ? sanitize_key( wp_unslash( $_POST['language'] ) ) : '';
		CSMF_I18n::set_language( $language );
		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$url = add_query_arg( array( 'post_type' => 'snow-monkey-forms', 'page' => self::PAGE_SLUG ), admin_url( 'edit.php' ) );
		if ( $form_id ) {
			$url = add_query_arg( 'form_id', $form_id, $url );
		}
		wp_safe_redirect( $url );
		exit;
	}

	/** @return int */
	private function requested_form_id() {
		$form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post = $form_id ? get_post( $form_id ) : null;
		if ( $post && 'snow-monkey-forms' === $post->post_type ) {
			return $form_id;
		}
		$forms = get_posts( array( 'post_type' => 'snow-monkey-forms', 'post_status' => array( 'publish', 'draft', 'private' ), 'posts_per_page' => 1, 'fields' => 'ids' ) );
		return $forms ? (int) $forms[0] : 0;
	}

	/** @return array<int,array<string,string>> */
	private function form_fields( $form_id ) {
		$post = get_post( $form_id );
		if ( ! $post ) { return array(); }
		$fields = array();
		$this->walk_blocks( parse_blocks( $post->post_content ), $fields );
		return array_values( $fields );
	}

	private function walk_blocks( array $blocks, array &$fields ) {
		foreach ( $blocks as $block ) {
			$name = $block['blockName'] ?? '';
			if ( 0 === strpos( $name, 'snow-monkey-forms/control-' ) && ! empty( $block['attrs']['name'] ) ) {
				$field_name = sanitize_text_field( $block['attrs']['name'] );
				$type = str_replace( 'snow-monkey-forms/control-', '', $name );
				$fields[ $field_name ] = array( 'name' => $field_name, 'type' => $type, 'label' => $field_name . ' (' . $type . ')' );
			}
			if ( ! empty( $block['innerBlocks'] ) ) { $this->walk_blocks( $block['innerBlocks'], $fields ); }
		}
	}
}
