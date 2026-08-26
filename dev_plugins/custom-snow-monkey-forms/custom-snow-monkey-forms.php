<?php
/**
 * Plugin Name: Custom Snow Monkey Forms
 * Plugin URI:  https://example.com/custom-snow-monkey-forms
 * Description: Snow Monkey Forms のリアルタイム検証、入力項目・送信先の条件分岐、画像添付を一元管理します。
 * Version:     2.1.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Requires Plugins: snow-monkey-forms
 * Author:      Site Development Team
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-snow-monkey-forms
 * Domain Path: /languages
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

define( 'CSMF_VERSION', '2.1.0' );
define( 'CSMF_FILE', __FILE__ );
define( 'CSMF_PATH', plugin_dir_path( __FILE__ ) );
define( 'CSMF_URL', plugin_dir_url( __FILE__ ) );

require_once CSMF_PATH . 'includes/class-config.php';
require_once CSMF_PATH . 'includes/class-condition-engine.php';
require_once CSMF_PATH . 'includes/class-i18n.php';
require_once CSMF_PATH . 'includes/class-integration.php';
require_once CSMF_PATH . 'includes/class-rest-api.php';
require_once CSMF_PATH . 'includes/class-admin.php';
require_once CSMF_PATH . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( 'CSMF_Plugin', 'activate' ) );

CSMF_Plugin::instance();
