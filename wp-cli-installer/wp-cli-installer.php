<?php
/**
 * Plugin Name: WP-CLI Installer
 * Description: Installs WP-CLI phar inside the plugin directory on activation.
 * Version: 0.2.0
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

    public static function init() {
        if (is_admin()) {
            add_action('admin_menu', [self::class, 'add_menu']);
            add_action('admin_post_wp_cli_installer_install', [self::class, 'handle_install']);
        }
    }

    public static function add_menu() {
        add_management_page(
            'WP-CLI Installer',
            'WP-CLI Installer',
            'manage_options',
            'wp-cli-installer',
            [self::class, 'render_page']
        );
    }

    public static function render_page() {
        $installed = isset($_GET['installed']);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WP-CLI Installer', 'wp-cli-installer'); ?></h1>
            <?php if ($installed) : ?>
                <div class="notice notice-success"><p><?php esc_html_e('WP-CLI installed successfully.', 'wp-cli-installer'); ?></p></div>
            <?php endif; ?>
            <?php if (file_exists(self::PHAR_PATH)) : ?>
                <p><?php printf(esc_html__('WP-CLI is installed at %s.', 'wp-cli-installer'), esc_html(self::PHAR_PATH)); ?></p>
            <?php else : ?>
                <p><?php esc_html_e('WP-CLI is not installed.', 'wp-cli-installer'); ?></p>
            <?php endif; ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('wp_cli_installer_install'); ?>
                <input type="hidden" name="action" value="wp_cli_installer_install" />
                <p><button class="button button-primary" type="submit"><?php esc_html_e('Install WP-CLI', 'wp-cli-installer'); ?></button></p>
            </form>
        </div>
        <?php
    }

    public static function handle_install() {
        check_admin_referer('wp_cli_installer_install');
        self::download_wp_cli();
        wp_redirect(add_query_arg('installed', '1', menu_page_url('wp-cli-installer', false)));
        exit;
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

// Initialize hooks
WP_CLI_Installer::init();
