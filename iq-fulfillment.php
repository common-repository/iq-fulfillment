<?php

/**
 * Plugin Name: IQ Fulfillment
 * Plugin URI: https://wordpress.org/plugins/iq-fulfillment
 * Description: A fulfillment solution for your platform.
 * Version: 1.0.1
 * Author: iqintegrations
 * Author URI: https://www.iqfulfillment.com/
 * Requires at least: 5.8
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class IQFulfillment
{
    const VERSION = '1.0.0';
    const API_URL = "https://iqintegrate.com";

    public function __construct()
    {
        $this->define_constants();
        add_action('admin_menu', array($this, 'add_menu'));
        register_activation_hook(__FILE__, [$this, 'activate']);
    }

    public function define_constants()
    {
        define('IQ_FULFILLMENT_VERSION', self::VERSION);
        define('IQ_FULFILLMENT_FILE', __FILE__);
        define('IQ_FULFILLMENT_PATH', __DIR__);
        define('IQ_FULFILLMENT_URL', plugins_url('', IQ_FULFILLMENT_FILE));
        define('IQ_FULFILLMENT_ASSETS', IQ_FULFILLMENT_URL . '/assets');
    }

    public function add_menu()
    {
        // Add menu item under "Tools"
        add_menu_page(
            'IQ Fulfillment',
            'IQ Fulfillment',
            'manage_options',
            'iq-fulfillment',
            array($this, 'iq_fulfillment_page'),
            IQ_FULFILLMENT_ASSETS . '/images/iq-icon.png',
        );
    }

    public function iq_fulfillment_page()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            if(!get_option('iq_fulfillment_activated')){
                $already_active = $this->check_integration_status();
                if(!$already_active){
                    update_option('iq_fulfillment_activated', false);
                    $template = __DIR__ . '/includes/Views/integrate.php';
                    $flag = true;
                }
                else{
                    update_option('iq_fulfillment_activated', true);
                    $template = __DIR__ . '/includes/Views/welcome.php';
                    $flag = false;
                }
            }else{
                $template = __DIR__ . '/includes/Views/welcome.php';
                $flag = false;
            }
        } else {
            $template = __DIR__ . '/includes/Views/warning.php';
            $flag = false;
        }
        if (file_exists($template)) {
            include $template;
        }

        if ($flag) {
            $this->process_data();
        }
    }

    public function process_data()
    {
        if (!isset($_POST['submit_integration_request'])) {
            return;
        }
        if (!wp_verify_nonce($_POST['_wpnonce'], 'try-integration')) {
            wp_die("Invalid action");
        }

        if (!current_user_can('manage_options')) {
            wp_die("Invalid premission");
        }

        $queries = http_build_query([
            "app_name" => "IQ Fulfillment",
            "scope" => "read_write",
            "user_id" => get_option('siteurl'),
            "return_url" => self::API_URL."/datahub/v1/woocommerce/auth/callback",
            "callback_url" => self::API_URL."/datahub/v1/woocommerce/auth/integration"
        ]);
        $response = wp_redirect(get_option('siteurl') . '/wc-auth/v1/authorize?' . $queries);
        if (is_wp_error($response)) {
            echo "error";
        }
    }

    public function check_integration_status()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'iq-fulfillment') {
            $response = wp_remote_get(self::API_URL.'/datahub/v1/woocommerce/auth/check/integration?site_url='.get_option('siteurl'));
            if (is_wp_error($response)) {
                return false;
            } else {
                $api_response = wp_remote_retrieve_body( $response );
                if($api_response)
                {
                    return true;
                }
            }
        }
        return false;
    }

    public function activate()
    {
        $installed = get_option('iq_fulfillment_installed');
        if (!$installed) {
            update_option('iq_fulfillment_installed', time());
        }
        update_option('iq_fulfillment_version', IQ_FULFILLMENT_VERSION);
        update_option('iq_fulfillment_activated', false);
    }

    // Deactivate function
    public static function deactivate()
    {
        update_option('iq_fulfillment_activated', false);
        $response = wp_remote_post (self::API_URL.'/datahub/v1/woocommerce/app/deactivate',[
            "body" => [
                "site_url" => get_option('siteurl')
            ]
        ]);
        if (is_wp_error($response)) {
            return false;
        } else {
            var_dump(wp_remote_retrieve_body( $response ));
            return true;
        }
    }

    // Uninstall function
    public static function uninstall()
    {
        update_option('iq_fulfillment_activated', false);
        $response = wp_remote_post (self::API_URL.'/datahub/v1/woocommerce/app/uninstall',[
            "body" => [
                "site_url" => get_option('siteurl')
            ]
        ]);
        if (is_wp_error($response)) {
            return false;
        } else {
            return true;
        }
    }

}

register_uninstall_hook( __FILE__, array( 'IQFulfillment', 'uninstall' ) );
register_deactivation_hook( __FILE__, array( 'IQFulfillment', 'deactivate' ) );
new IQFulfillment();