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

        add_settings_field(
            'known_lists',
            __('Saved Lists (Optional)', 'simple-sendy-ses-bridge'),
            array($this, 'render_textarea_field'),
            'simple_sendy_bridge',
            'sssb_main_section',
            array(
                'field' => 'known_lists',
                'desc' => __('Enter your lists one per line in the format: <strong>List Name, List ID</strong> (or use | separator)<br>Example:<br>Main Subscribers, l12345<br>All Lists|l12345,l67890', 'simple-sendy-ses-bridge')
            )
        );

        // --- Footer & Social Settings ---
        
        add_settings_section(
            'sssb_footer_section',
            __('Footer & Social Settings', 'simple-sendy-ses-bridge'),
            null,
            'simple_sendy_bridge'
        );

        // Register individual options for footer settings
        register_setting('sssb_settings_group', 'sssb_footer_logo_url', 'esc_url_raw');
        register_setting('sssb_settings_group', 'sssb_footer_copyright', 'sanitize_text_field');
        register_setting('sssb_settings_group', 'sssb_more_articles_link', 'esc_url_raw');
        register_setting('sssb_settings_group', 'sssb_social_instagram', 'esc_url_raw');
        register_setting('sssb_settings_group', 'sssb_social_linkedin', 'esc_url_raw');
        register_setting('sssb_settings_group', 'sssb_social_twitter', 'esc_url_raw');
        register_setting('sssb_settings_group', 'sssb_social_youtube', 'esc_url_raw');

        add_settings_field('sssb_footer_logo_url', __('Footer Logo URL', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_footer_logo_url'));
        add_settings_field('sssb_footer_copyright', __('Copyright Text', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_footer_copyright'));
        add_settings_field('sssb_more_articles_link', __('"Read More Articles" Link', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_more_articles_link'));
        
        add_settings_field('sssb_social_instagram', __('Instagram URL', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_social_instagram'));
        add_settings_field('sssb_social_linkedin', __('LinkedIn URL', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_social_linkedin'));
        add_settings_field('sssb_social_twitter', __('X (Twitter) URL', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_social_twitter'));
        add_settings_field('sssb_social_youtube', __('YouTube URL', 'simple-sendy-ses-bridge'), array($this, 'render_footer_field'), 'simple_sendy_bridge', 'sssb_footer_section', array('field' => 'sssb_social_youtube'));
    }

    public function render_footer_field($args) {
        $option = get_option($args['field']);
        echo '<input type="text" name="' . esc_attr($args['field']) . '" value="' . esc_attr($option) . '" class="regular-text">';
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
        if (isset($input['from_name'])) {
            $new_input['from_name'] = sanitize_text_field($input['from_name']);
        }
        if (isset($input['from_email'])) {
            $new_input['from_email'] = sanitize_email($input['from_email']);
        }
        if (isset($input['known_lists'])) {
            // Allow basic text, newlines, and pipes
            $new_input['known_lists'] = wp_strip_all_tags($input['known_lists']);
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
                <?php echo wp_kses_post($desc); ?>
            </p>
        <?php endif; ?>
    <?php
    }

    public function render_textarea_field($args)
    {
        $options = get_option('sssb_settings');
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $desc = isset($args['desc']) ? $args['desc'] : '';
        ?>
        <textarea name="sssb_settings[<?php echo esc_attr($field); ?>]" rows="5" cols="50" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php if ($desc): ?>
            <p class="description">
                <?php echo wp_kses_post($desc); ?>
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
            
            <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;">
                <h3><?php esc_html_e('Support', 'simple-sendy-ses-bridge'); ?></h3>
                <p><?php esc_html_e('If you like this plugin, please consider buying me a coffee!', 'simple-sendy-ses-bridge'); ?></p>
                <p>
                    <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" class="button button-default">
                        <?php esc_html_e('☕ Buy Me A Coffee', 'simple-sendy-ses-bridge'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }
}
