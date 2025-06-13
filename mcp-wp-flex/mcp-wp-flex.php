<?php
/**
 * Plugin Name: MCP WP Flex
 * Description: WordPress integration for MCP server using ReactPHP.
 * Version: 0.1.0
 * Author: OpenAI Codex
 * License: GPLv3 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Use composer's autoload if available
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

use React\EventLoop\Loop;
use React\Http\Browser;
use React\Http\Server as ReactServer;
use React\Socket\SocketServer;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class MCP_WP_Flex {
    /**
     * Initialize plugin hooks
     */
    public function __construct() {
        add_action('init', [$this, 'init']);
    }

    /**
     * Plugin initialization hook
     */
    public function init() {
        // Placeholder: start ReactPHP server when invoked via CLI (e.g., WP-CLI)
        if (php_sapi_name() === 'cli' && defined('MCP_WP_FLEX_SERVER') && MCP_WP_FLEX_SERVER) {
            $this->start_server();
        }
    }

    /**
     * Start a simple ReactPHP HTTP server
     */
    public function start_server() {
        $loop = Loop::get();

        $server = new ReactServer(function (ServerRequestInterface $request) {
            // TODO: integrate with MCP server logic and Claude AI
            $data = [
                'message' => 'MCP WP Flex server running',
                'path' => $request->getUri()->getPath(),
            ];

            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($data)
            );
        });

        $socket = new SocketServer('0.0.0.0:8080');
        $server->listen($socket);

        // Output message to CLI
        echo "MCP WP Flex server listening on http://0.0.0.0:8080\n";
        $loop->run();
    }

    /**
     * Send a request to an MCP server using ReactPHP's Browser
     *
     * @param array $payload Data to send
     * @return \React\Promise\PromiseInterface
     */
    public function send_to_mcp(array $payload) {
        $browser = new Browser();
        $url = apply_filters('mcp_wp_flex_server_url', 'http://localhost:4000');

        return $browser->post(
            $url,
            ['Content-Type' => 'application/json'],
            json_encode($payload)
        );
    }
}

// Initialize plugin
new MCP_WP_Flex();
