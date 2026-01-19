<?php
/**
 * Plugin Name: Simple Sendy SES Bridge
 * Plugin URI:  https://example.com/simple-sendy-ses-bridge
 * Description: Connects WordPress to Sendy (via Amazon SES) to create and send newsletters from your content.
 * Version:     1.0.0
 * Author:      Gunjan Jaswal
 * Author URI:  https://gunjanjaswal.me
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: simple-sendy-ses-bridge
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Define plugin constants.
define('SSSB_VERSION', '1.0.0');
define('SSSB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSSB_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class Simple_Sendy_SES_Bridge
{

	/**
	 * Instance of the class.
	 *
	 * @var Simple_Sendy_SES_Bridge
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Simple_Sendy_SES_Bridge
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required files.
	 */
	private function includes()
	{
		require_once SSSB_PLUGIN_DIR . 'includes/class-sendy-api.php';
		require_once SSSB_PLUGIN_DIR . 'includes/class-admin-settings.php';
		require_once SSSB_PLUGIN_DIR . 'includes/class-newsletter-builder.php';
	}

	/**
	 * Init hooks.
	 */
	private function init_hooks()
	{
		add_action('plugins_loaded', array($this, 'load_textdomain'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
	}

	/**
	 * Load text domain.
	 */
	public function load_textdomain()
	{
		load_plugin_textdomain('simple-sendy-ses-bridge', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Enqueue admin scripts and styles.
	 */
	public function enqueue_admin_assets($hook)
	{
		// Only load on our plugin pages
		$screen = get_current_screen();
		if (!$screen || false === strpos($screen->base, 'simple_sendy_bridge')) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style('sssb-admin-style', SSSB_PLUGIN_URL . 'admin/css/style.css', array(), SSSB_VERSION);
		wp_enqueue_script('sssb-admin-script', SSSB_PLUGIN_URL . 'admin/js/script.js', array('jquery'), SSSB_VERSION, true);

		wp_localize_script('sssb-admin-script', 'sssb_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('sssb_newsletter_nonce')
		));
	}
}

// Initialize the plugin.
function sssb_init()
{
	return Simple_Sendy_SES_Bridge::get_instance();
}
add_action('plugins_loaded', 'sssb_init');
