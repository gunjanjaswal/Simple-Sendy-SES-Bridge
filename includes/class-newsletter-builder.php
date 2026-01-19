<?php
/**
 * Newsletter Builder Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSSB_Newsletter_Builder
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action('wp_ajax_sssb_search_posts', array($this, 'ajax_search_posts'));
        add_action('wp_ajax_sssb_create_campaign', array($this, 'ajax_create_campaign'));
    }

    public function add_submenu()
    {
        add_submenu_page(
            'simple_sendy_bridge',
            __('Create Newsletter', 'simple-sendy-ses-bridge'),
            __('Create Newsletter', 'simple-sendy-ses-bridge'),
            'manage_options',
            'sssb_newsletter_builder',
            array($this, 'render_page')
        );
    }

    public function render_page()
    {
        $options = get_option('sssb_settings');
        $default_from_name = isset($options['from_name']) ? $options['from_name'] : '';
        $default_from_email = isset($options['from_email']) ? $options['from_email'] : '';
        $default_list_id = isset($options['list_id']) ? $options['list_id'] : '';
        ?>
        <div class="wrap sssb-container">
            <h1>
                <?php esc_html_e('Create Newsletter', 'simple-sendy-ses-bridge'); ?>
            </h1>

            <div class="sssb-flex">
                <!-- Left Column: Controls -->
                <div class="sssb-col-left">
                    <div class="sssb-card">
                        <h2>
                            <?php esc_html_e('Campaign Settings', 'simple-sendy-ses-bridge'); ?>
                        </h2>
                        <p>
                            <label>
                                <?php esc_html_e('Subject Line', 'simple-sendy-ses-bridge'); ?>
                            </label><br>
                            <input type="text" id="sssb-subject" class="widefat" placeholder="Newsletter Subject">
                        </p>
                        <p>
                            <label>
                                <?php esc_html_e('From Name', 'simple-sendy-ses-bridge'); ?>
                            </label><br>
                            <input type="text" id="sssb-from-name" class="widefat"
                                value="<?php echo esc_attr($default_from_name); ?>">
                        </p>
                        <p>
                            <label>
                                <?php esc_html_e('From Email', 'simple-sendy-ses-bridge'); ?>
                            </label><br>
                            <input type="email" id="sssb-from-email" class="widefat"
                                value="<?php echo esc_attr($default_from_email); ?>">
                        </p>
                        <p>
                            <label>
                                <?php esc_html_e('List ID', 'simple-sendy-ses-bridge'); ?>
                            </label><br>
                            <input type="text" id="sssb-list-id" class="widefat"
                                value="<?php echo esc_attr($default_list_id); ?>">
                        </p>
                    </div>

                    <div class="sssb-card">
                        <h2><?php esc_html_e('Design Settings', 'simple-sendy-ses-bridge'); ?></h2>
                        <p>
                            <label><strong><?php esc_html_e('Banner Image', 'simple-sendy-ses-bridge'); ?></strong></label><br>
                            <button class="button"
                                id="sssb-upload-banner"><?php esc_html_e('Select Banner', 'simple-sendy-ses-bridge'); ?></button>
                            <button class="button hidden" id="sssb-remove-banner"
                                style="display:none; color: #a00; border-color: #a00;"><?php esc_html_e('Remove', 'simple-sendy-ses-bridge'); ?></button>
                        <p class="description" style="margin-top: 5px; color: #666; font-style: italic;">
                            <?php esc_html_e('Recommended Size: 600px wide. Keep height under 200px for best results on mobile and desktop.', 'simple-sendy-ses-bridge'); ?>
                        </p>
                        <input type="hidden" id="sssb-banner-url">
                        <div id="sssb-banner-preview" style="margin-top:10px; max-width:100%;"></div>
                        </p>
                        <p>
                            <label><strong><?php esc_html_e('Layout Style', 'simple-sendy-ses-bridge'); ?></strong></label><br>
                            <label><input type="radio" name="sssb_layout" value="list" checked>
                                <?php esc_html_e('Simple List', 'simple-sendy-ses-bridge'); ?></label><br>
                            <label><input type="radio" name="sssb_layout" value="grid">
                                <?php esc_html_e('Grid (2 Columns)', 'simple-sendy-ses-bridge'); ?></label><br>
                            <label><input type="radio" name="sssb_layout" value="full">
                                <?php esc_html_e('Full Content', 'simple-sendy-ses-bridge'); ?></label>
                        </p>
                    </div>

                    <div class="sssb-card">
                        <h2>
                            <?php esc_html_e('Add Posts', 'simple-sendy-ses-bridge'); ?>
                        </h2>
                        <input type="text" id="sssb-search" class="widefat" placeholder="Search posts...">
                        <div id="sssb-post-results" class="sssb-post-list">
                            <!-- Search results -->
                        </div>
                    </div>

                    <div class="sssb-card">
                        <h2>
                            <?php esc_html_e('Selected Posts', 'simple-sendy-ses-bridge'); ?>
                        </h2>
                        <div id="sssb-selected-list" class="sssb-selected-posts"></div>
                    </div>

                    <div class="sssb-card">
                        <h2>
                            <?php esc_html_e('Actions', 'simple-sendy-ses-bridge'); ?>
                        </h2>
                        <label><input type="radio" name="sssb_send_type" value="draft" checked>
                            <?php esc_html_e('Save as Draft in Sendy', 'simple-sendy-ses-bridge'); ?>
                        </label><br>
                        <label><input type="radio" name="sssb_send_type" value="send">
                            <?php esc_html_e('Send Immediately', 'simple-sendy-ses-bridge'); ?>
                        </label><br><br>
                        <button id="sssb-create-campaign" class="button button-primary large">
                            <?php esc_html_e('Create Campaign', 'simple-sendy-ses-bridge'); ?>
                        </button>
                    </div>

                    <div class="sssb-card">
                        <h3><?php esc_html_e('Support & Contact', 'simple-sendy-ses-bridge'); ?></h3>
                        <p>
                            <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank">
                                <img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee"
                                    style="height: 40px !important; width: auto !important;">
                            </a>
                        </p>
                        <p style="margin-top:15px;">
                            <strong><?php esc_html_e('Email:', 'simple-sendy-ses-bridge'); ?></strong> <a
                                href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a><br>
                            <strong><?php esc_html_e('Website:', 'simple-sendy-ses-bridge'); ?></strong> <a
                                href="https://gunjanjaswal.me" target="_blank">gunjanjaswal.me</a>
                        </p>
                    </div>
                </div>

                <!-- Right Column: Preview -->
                <div class="sssb-col-right">
                    <div class="sssb-card">
                        <h2>
                            <?php esc_html_e('Email Preview', 'simple-sendy-ses-bridge'); ?>
                        </h2>
                        <div id="sssb-preview-content">
                            <p style="text-align:center; color:#999;">
                                <?php esc_html_e('Add posts to see preview', 'simple-sendy-ses-bridge'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function ajax_search_posts()
    {
        check_ajax_referer('sssb_newsletter_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permission denied');
        }

        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            's' => $query,
            'posts_per_page' => 10,
        );

        $posts = get_posts($args);
        $data = array();

        foreach ($posts as $post) {
            $thumb_id = get_post_thumbnail_id($post->ID);
            $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';

            // Fallback if no thumb
            if (empty($thumb_url))
                $thumb_url = 'https://via.placeholder.com/150';

            $data[] = array(
                'id' => $post->ID,
                'title' => get_the_title($post->ID),
                'thumbnail' => $thumb_url,
                'excerpt' => wp_trim_words($post->post_excerpt ? $post->post_excerpt : strip_shortcodes($post->post_content), 20),
                'link' => get_permalink($post->ID),
            );
        }

        wp_send_json_success($data);
    }

    public function ajax_create_campaign()
    {
        check_ajax_referer('sssb_newsletter_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $campaign_data = $_POST['campaign']; // Basic sanitization needed, but HTML allows for email content

        // Prepare args for API
        $args = array(
            'from_name' => sanitize_text_field($campaign_data['from_name']),
            'from_email' => sanitize_email($campaign_data['from_email']),
            'subject' => sanitize_text_field($campaign_data['subject']),
            'html_text' => wp_kses_post($campaign_data['html_text']), // Use wp_kses_post to allow safe HTML
            'plain_text' => sanitize_textarea_field($campaign_data['plain_text']),
            'list_ids' => sanitize_text_field($campaign_data['list_id']),
            'send_campaign' => ($campaign_data['send_type'] === 'send') ? 1 : 0
        );

        $sendy_api = new SSSB_Sendy_API();
        $result = $sendy_api->create_campaign($args);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } else {
            wp_send_json_success(array('message' => 'Campaign created successfully!'));
        }
    }
}
