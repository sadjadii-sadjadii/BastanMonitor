<?php
/**
 * Plugin Name: Bastan Agent
 * Description: Secure communication agent for connecting a WordPress site to the Bastan Monitor central dashboard.
 * Version: 1.0.0
 * Author: BastanGraphic
 * Author URI: https://bastangraphic.com
 * License: GNU General Public License version 2 or later; see LICENSE.txt
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

class Bastan_Agent_WP {
    
    // Enter your security token here (must match the token registered in the Monitor dashboard)
    private $token = 'YOUR_SECRET_TOKEN_HERE'; 

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('bastan/v1', '/monitor', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_metrics'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function get_metrics($request) {
        global $wpdb;
        $request_token = $request->get_param('token');

        // 1. Check security token
        if ($request_token !== $this->token) {
            return new WP_Error('rest_forbidden', 'Access Denied. Invalid token.', ['status' => 403]);
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $response = [
            'status'          => 'success',
            'cms'             => 'WordPress',
            'url'             => site_url(),
            'version'         => get_bloginfo('version'),
            'php_version'     => PHP_VERSION,
            'plugins'         => [],
            'themes'          => [],
            'security_logs'   => [],
            'pending_updates' => [],
            'last_backup'     => null,
            'backup_debug'    => null,
            'fim'             => [],
            'has_firewall'    => false,
            'firewall_type'   => null
        ];

        // 2. File Integrity Monitoring (FIM)
        $files_to_check = [
            ABSPATH . 'wp-config.php',
            ABSPATH . 'index.php',
            ABSPATH . '.htaccess',
        ];
        foreach ($files_to_check as $file) {
            $key = str_replace(ABSPATH, '', $file);
            $response['fim'][$key] = file_exists($file) ? md5_file($file) : 'missing';
        }

        // 3. Read plugins status
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);
        
        foreach ($all_plugins as $path => $plugin_data) {
            $response['plugins'][] = [
                'name'    => $plugin_data['Name'],
                'element' => dirname($path),
                'version' => $plugin_data['Version'],
                'status'  => in_array($path, $active_plugins) ? 'Active' : 'Inactive',
                'type'    => 'plugin'
            ];
        }

        // 4. Read themes status
        $all_themes = wp_get_themes();
        $current_theme = get_stylesheet();
        
        foreach ($all_themes as $stylesheet => $theme_data) {
            $response['themes'][] = [
                'name'    => $theme_data->get('Name'),
                'element' => $stylesheet,
                'version' => $theme_data->get('Version'),
                'status'  => ($stylesheet === $current_theme) ? 'Active' : 'Inactive',
                'type'    => 'template'
            ];
        }

        // 5. Check pending updates
        $plugin_updates = get_site_transient('update_plugins');
        if (!empty($plugin_updates->response)) {
            foreach ($plugin_updates->response as $path => $data) {
                $response['pending_updates'][dirname($path)] = $data->new_version;
            }
        }
        
        $theme_updates = get_site_transient('update_themes');
        if (!empty($theme_updates->response)) {
            foreach ($theme_updates->response as $stylesheet => $data) {
                $response['pending_updates'][$stylesheet] = $data['new_version'];
            }
        }

        $core_updates = get_site_transient('update_core');
        if (!empty($core_updates->updates) && isset($core_updates->updates[0]) && $core_updates->updates[0]->response === 'upgrade') {
            $response['pending_updates']['wordpress_core'] = $core_updates->updates[0]->current;
        }

        // 6. Check security status (BBQ Firewall)
        if (in_array('bbq-firewall/bbq.php', $active_plugins) || in_array('bbq-pro/bbq-pro.php', $active_plugins)) {
            $response['has_firewall'] = true;
            $response['firewall_type'] = 'BBQ Firewall';
        }

        // 7. Check latest backup (Duplicator)
        $duplicator_table = $wpdb->prefix . 'duplicator_packages';
        if ($wpdb->get_var("SHOW TABLES LIKE '$duplicator_table'") === $duplicator_table) {
            // Get the last package with a complete status (100)
            $last_backup = $wpdb->get_row("SELECT created FROM $duplicator_table WHERE status >= 100 ORDER BY id DESC LIMIT 1");
            if ($last_backup && !empty($last_backup->created)) {
                $response['last_backup'] = $last_backup->created;
            }
        }

        return rest_ensure_response($response);
    }
}

new Bastan_Agent_WP();