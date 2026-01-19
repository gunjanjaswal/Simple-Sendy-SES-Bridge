<?php
/**
 * Admin Settings Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSSB_Admin_Settings
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu()
    {
        add_menu_page(
            __('Sendy Bridge', 'simple-sendy-ses-bridge'),
            __('Sendy Bridge', 'simple-sendy-ses-bridge'),
            'manage_options',
            'simple_sendy_bridge',
            array($this, 'settings_page_html'),
            'dashicons-email',
            60
        );

        add_submenu_page(
            'simple_sendy_bridge',
            __('Settings', 'simple-sendy-ses-bridge'),
            __('Settings', 'simple-sendy-ses-bridge'),
            'manage_options',
            'simple_sendy_bridge'
        );
    }

    public function register_settings()
    {
        register_setting('sssb_settings_group', 'sssb_settings', array($this, 'sanitize_settings'));

        add_settings_section(
            'sssb_main_section',
            __('Sendy Connection Settings', 'simple-sendy-ses-bridge'),
            null,
            'simple_sendy_bridge'
        );

        add_settings_field(
            'installation_url',
            __('Sendy Installation URL', 'simple-sendy-ses-bridge'),
            array($this, 'render_text_field'),
            'simple_sendy_bridge',
            'sssb_main_section',
            array('field' => 'installation_url', 'desc' => 'E.g. https://sendy.yourdomain.com/')
        );

        add_settings_field(
            'api_key',
            __('API Key', 'simple-sendy-ses-bridge'),
            array($this, 'render_text_field'),
            'simple_sendy_bridge',
            'sssb_main_section',
            array('field' => 'api_key', 'type' => 'password')
        );

        add_settings_field(
            'list_id',
            __('Default List ID', 'simple-sendy-ses-bridge'),
            array($this, 'render_text_field'),
            'simple_sendy_bridge',
            'sssb_main_section',
            array('field' => 'list_id')
        );

        add_settings_field(
            'from_name',
            __('Default From Name', 'simple-sendy-ses-bridge'),
            array($this, 'render_text_field'),
            'simple_sendy_bridge',
            'sssb_main_section',
            array('field' => 'from_name')
        );

        add_settings_field(
            'from_email',
            __('Default From Email', 'simple-sendy-ses-bridge'),
            array($this, 'render_text_field'),
            'simple_sendy_bridge',
            'sssb_main_section',
            array('field' => 'from_email')
        );
    }

    public function sanitize_settings($input)
    {
        $new_input = array();
        if (isset($input['installation_url'])) {
            $new_input['installation_url'] = esc_url_raw($input['installation_url']);
        }
        if (isset($input['api_key'])) {
            $new_input['api_key'] = sanitize_text_field($input['api_key']);
        }
        if (isset($input['list_id'])) {
            $new_input['list_id'] = sanitize_text_field($input['list_id']);
        }
        if (isset($input['from_name'])) {
            $new_input['from_name'] = sanitize_text_field($input['from_name']);
        }
        if (isset($input['from_email'])) {
            $new_input['from_email'] = sanitize_email($input['from_email']);
        }
        return $new_input;
    }

    public function render_text_field($args)
    {
        $options = get_option('sssb_settings');
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $type = isset($args['type']) ? $args['type'] : 'text';
        $desc = isset($args['desc']) ? $args['desc'] : '';
        ?>
        <input type="<?php echo esc_attr($type); ?>" name="sssb_settings[<?php echo esc_attr($field); ?>]"
            value="<?php echo esc_attr($value); ?>" class="regular-text">
        <?php if ($desc): ?>
            <p class="description">
                <?php echo esc_html($desc); ?>
            </p>
        <?php endif; ?>
    <?php
    }

    public function settings_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('sssb_settings_group');
                do_settings_sections('simple_sendy_bridge');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }
}
