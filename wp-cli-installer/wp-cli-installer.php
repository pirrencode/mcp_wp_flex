<?php
/**
 * Plugin Name: WP-CLI Installer
 * Description: Installs WP-CLI phar inside the plugin directory on activation.
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
}

register_activation_hook(__FILE__, ['WP_CLI_Installer', 'activate']);
