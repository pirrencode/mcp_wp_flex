<?php
/**
 * Plugin Name: WP-CLI Installer
 * Description: Installs WP-CLI phar inside the plugin directory on activation and provides a dashboard interface.
 * Version: 0.1.0
 * Author: OpenAI Codex
 * License: GPLv3 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WP_CLI_Installer {
    const PHAR_URL = 'https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar';
    const PHAR_PATH = __DIR__ . '/wp-cli.phar';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_wp_cli_command'));
    }

    public static function activate() {
        if (!file_exists(self::PHAR_PATH)) {
            self::download_wp_cli();
        }
    }

    private static function download_wp_cli() {
        $response = wp_remote_get(self::PHAR_URL);
        if (is_wp_error($response)) {
            error_log('WP-CLI download failed: ' . $response->get_error_message());
            return;
        }
        $body = wp_remote_retrieve_body($response);
        if ($body) {
            file_put_contents(self::PHAR_PATH, $body);
            chmod(self::PHAR_PATH, 0755);
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            'WP-CLI Dashboard',
            'WP-CLI',
            'manage_options',
            'wp-cli-dashboard',
            array($this, 'render_admin_page'),
            'dashicons-terminal',
            30
        );
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>WP-CLI Dashboard</h1>
            <form method="post" action="">
                <?php wp_nonce_field('wp_cli_command', 'wp_cli_nonce'); ?>
                <div class="form-field">
                    <label for="wp_cli_command">Enter WP-CLI Command:</label>
                    <input type="text" name="wp_cli_command" id="wp_cli_command" class="regular-text" 
                           placeholder="e.g., wp plugin list" required>
                </div>
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Execute Command">
                </p>
            </form>

            <?php
            if (isset($_POST['wp_cli_command']) && check_admin_referer('wp_cli_command', 'wp_cli_nonce')) {
                $command = sanitize_text_field($_POST['wp_cli_command']);
                $this->execute_wp_cli_command($command);
            }
            ?>
        </div>
        <?php
    }

    public function handle_wp_cli_command() {
        if (isset($_POST['wp_cli_command']) && check_admin_referer('wp_cli_command', 'wp_cli_nonce')) {
            $command = sanitize_text_field($_POST['wp_cli_command']);
            $this->execute_wp_cli_command($command);
        }
    }

    private function execute_wp_cli_command($command) {
        if (!file_exists(self::PHAR_PATH)) {
            echo '<div class="notice notice-error"><p>WP-CLI phar file not found. Please deactivate and reactivate the plugin.</p></div>';
            return;
        }

        // Remove 'wp' from the beginning of the command if it exists
        $command = preg_replace('/^wp\s+/', '', $command);
        
        // Execute the command
        $output = array();
        $return_var = 0;
        $full_command = 'php ' . escapeshellarg(self::PHAR_PATH) . ' ' . escapeshellarg($command);
        
        exec($full_command . ' 2>&1', $output, $return_var);
        
        echo '<div class="notice notice-info"><p>Command executed: ' . esc_html($command) . '</p></div>';
        echo '<div class="wp-cli-output" style="background: #f0f0f1; padding: 10px; margin: 10px 0; border: 1px solid #c3c4c7;">';
        echo '<pre>' . esc_html(implode("\n", $output)) . '</pre>';
        echo '</div>';
    }
}

// Initialize the plugin
$wp_cli_installer = new WP_CLI_Installer();
register_activation_hook(__FILE__, ['WP_CLI_Installer', 'activate']);
