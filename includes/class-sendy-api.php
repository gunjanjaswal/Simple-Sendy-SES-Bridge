<?php
/**
 * Sendy API Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSSB_Sendy_API
{

    private $installation_url;
    private $api_key;
    private $list_id;

    public function __construct()
    {
        $options = get_option('sssb_settings');
        $this->installation_url = isset($options['installation_url']) ? trailingslashit($options['installation_url']) : '';
        $this->api_key = isset($options['api_key']) ? $options['api_key'] : '';
        $this->list_id = isset($options['list_id']) ? $options['list_id'] : '';
    }

    /**
     * Create and optionally send a campaign.
     * 
     * @param array $args
     * @return array|WP_Error
     */
    public function create_campaign($args)
    {
        if (empty($this->installation_url) || empty($this->api_key)) {
            return new WP_Error('sssb_missing_config', __('Sendy settings are incomplete.', 'simple-sendy-ses-bridge'));
        }

        $defaults = array(
            'from_name' => '',
            'from_email' => '',
            'reply_to' => '',
            'title' => '', // Subject
            'subject' => '', // Subject (Sendy uses 'subject' or 'title' depending on version/endpoint, usually 'subject' for creation)
            'plain_text' => '',
            'html_text' => '',
            'list_ids' => $this->list_id,
            'brand_id' => '', // Optional
            'query_string' => '', // Optional
            'send_campaign' => 0, // 0 for draft, 1 for send
            'api_key' => $this->api_key,
        );

        $body = wp_parse_args($args, $defaults);

        // Ensure subject is set
        if (empty($body['subject']) && !empty($body['title'])) {
            $body['subject'] = $body['title'];
        }

        $endpoint = $this->installation_url . 'api/campaigns/create.php';

        $response = wp_remote_post($endpoint, array(
            'body' => $body,
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $response_body = wp_remote_retrieve_body($response);

        if ('Campaign created' === $response_body || 'Campaign created and now sending' === $response_body) {
            return array(
                'success' => true,
                'message' => $response_body,
            );
        } else {
            return new WP_Error('sssb_sendy_error', $response_body);
        }
    }

    /**
     * Get subscriber count (simple test method)
     */
    public function get_subscriber_count()
    {
        if (empty($this->installation_url) || empty($this->api_key) || empty($this->list_id)) {
            return false;
        }

        $endpoint = $this->installation_url . 'api/subscribers/active-subscriber-count.php';

        $body = array(
            'api_key' => $this->api_key,
            'list_id' => $this->list_id
        );

        $response = wp_remote_post($endpoint, array(
            'body' => $body
        ));

        if (is_wp_error($response)) {
            return false;
        }

        return wp_remote_retrieve_body($response);
    }
}
