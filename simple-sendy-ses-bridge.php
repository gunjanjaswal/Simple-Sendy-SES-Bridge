<?php
/**
 * Plugin Name: Simple Sendy SES Bridge
 * Plugin URI:  https://gunjanjaswal.me
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
class SSSB_Core
{

	/**
	 * Instance of the class.
	 *
	 * @var SSSB_Core
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return SSSB_Core
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
    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->includes();
        $this->init_hooks();
        $this->instantiate_classes();
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
     * Instantiate classes.
     */
    private function instantiate_classes()
    {
        if (is_admin()) {
            new SSSB_Admin_Settings();
            new SSSB_Newsletter_Builder();
        }
    }

    /**
     * Init hooks.
     */
    private function init_hooks()
    {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('init', array($this, 'register_post_type'));
        add_action('sssb_send_scheduled_campaign', array($this, 'send_scheduled_campaign'));
    }

    /**
     * Register Custom Post Type for Campaigns
     */
    public function register_post_type()
    {
        register_post_type(
            'sssb_campaign',
            array(
                'labels'      => array(
                    'name'          => __('Campaigns', 'simple-sendy-ses-bridge'),
                    'singular_name' => __('Campaign', 'simple-sendy-ses-bridge'),
                ),
                'public'      => false,
                'show_ui'     => true, // Show in admin to let user see history/status
                'show_in_menu' => 'simple_sendy_bridge',
                'supports'    => array('title', 'custom-fields', 'editor'), // editor can hold HTML content
                'capability_type' => 'post',
                'capabilities' => array(
                    'create_posts' => false, // Only created via code
                ),
                'map_meta_cap' => true,
            )
        );
    }

    /**
     * Handle Scheduled Campaign Sending
     */
    public function send_scheduled_campaign($post_id)
    {
        // Check if already sent to avoid duplicates (though CPT status should handle this)
        if (get_post_meta($post_id, '_sssb_status', true) === 'sent') {
            return;
        }

        $campaign_data = array(
            'from_name' => get_post_meta($post_id, '_sssb_from_name', true),
            'from_email' => get_post_meta($post_id, '_sssb_from_email', true),
            'subject' => get_the_title($post_id),
            'html_text' => get_post_field('post_content', $post_id),
            'plain_text' => get_post_meta($post_id, '_sssb_plain_text', true),
            'list_ids' => get_post_meta($post_id, '_sssb_list_id', true),
            'send_campaign' => 1 // Always send when triggered by schedule
        );

        $sendy_api = new SSSB_Sendy_API();
        $result = $sendy_api->create_campaign($campaign_data);

        if (is_wp_error($result)) {
            // Log error
            update_post_meta($post_id, '_sssb_send_error', $result->get_error_message());
            update_post_meta($post_id, '_sssb_status', 'failed');
        } else {
            // Mark as sent
            update_post_meta($post_id, '_sssb_status', 'sent');
            update_post_meta($post_id, '_sssb_sent_time', current_time('mysql'));
        }
    }

    /**
     * Load text domain.
     */
    public function load_textdomain()
    {
        // load_plugin_textdomain('simple-sendy-ses-bridge', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_admin_assets($hook)
    {
        // Only load on our plugin pages
        $screen = get_current_screen();
        
        // Check if we are on the settings page or builder page
        $is_plugin_page = false;
        if ($screen && (
            strpos($screen->base, 'simple_sendy_bridge') !== false || 
            strpos($screen->base, 'sssb_newsletter_builder') !== false ||
            'sssb_campaign' === $screen->post_type
        )) {
            $is_plugin_page = true;
        }

        if (!$is_plugin_page) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('sssb-admin-style', SSSB_PLUGIN_URL . 'admin/css/style.css', array(), SSSB_VERSION);
        wp_enqueue_script('sssb-admin-script', SSSB_PLUGIN_URL . 'admin/js/script.js', array('jquery', 'jquery-ui-datepicker'), SSSB_VERSION, true);
        // wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        
        $known_lists = array();
        $options = get_option('sssb_settings');
        if (!empty($options['known_lists'])) {
            $lines = explode("\n", $options['known_lists']);
            foreach ($lines as $line) {
                // Determine separator: pipe or comma
                $separator = (strpos($line, '|') !== false) ? '|' : ',';
                
                $parts = explode($separator, $line);
                if (count($parts) >= 2) {
                    $known_lists[] = array(
                        'name' => trim($parts[0]),
                        'id' => trim($parts[1])
                    );
                }
            }
        }
        
        wp_localize_script('sssb-admin-script', 'sssb_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sssb_newsletter_nonce'),
            'known_lists' => $known_lists,
            'settings' => array(
                'footer_logo' => get_option('sssb_footer_logo_url'),
                'footer_copyright' => get_option('sssb_footer_copyright', '© {year} ' . get_bloginfo('name')),
                'more_articles_link' => get_option('sssb_more_articles_link'),
                'social_instagram' => get_option('sssb_social_instagram'),
                'social_linkedin' => get_option('sssb_social_linkedin'),
                'social_twitter' => get_option('sssb_social_twitter'),
                'social_youtube' => get_option('sssb_social_youtube'),
            )
        ));
    }
}

// Initialize the plugin.
function sssb_init()
{
    return SSSB_Core::get_instance();
}
add_action('plugins_loaded', 'sssb_init');

// Add Buy Me A Coffee link to plugin row meta
add_filter('plugin_row_meta', 'sssb_plugin_row_meta', 10, 2);
function sssb_plugin_row_meta($links, $file)
{
    if (strpos($file, 'simple-sendy-ses-bridge.php') !== false) {
        $new_links = array(
            'buy_coffee' => '<a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" style="color: #d35400; font-weight: bold;">☕ Buy me a coffee</a>'
        );
        $links = array_merge($links, $new_links);
    }
    return $links;
}

// Add Settings link to plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sssb_plugin_action_links');
function sssb_plugin_action_links($links)
{
    $settings_link = '<a href="' . admin_url('admin.php?page=simple_sendy_bridge') . '">' . __('Settings', 'simple-sendy-ses-bridge') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
