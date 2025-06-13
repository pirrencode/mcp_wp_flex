# MCP WP Flex

WordPress plugin that demonstrates how to integrate with the [MCP server](https://github.com/php-mcp/server) using [ReactPHP](https://reactphp.org/).

## Installation

1. Install PHP dependencies using Composer:

```bash
composer install
```

2. Copy the `mcp-wp-flex` directory into your WordPress `plugins` folder.

5. In the WordPress admin go to **Tools → WP-CLI Installer** and click **Install WP-CLI** to download the `wp-cli.phar` file.
The **WP-CLI Installer** plugin downloads the `wp-cli.phar` file into its own directory. It provides a page under **Tools → WP-CLI Installer** with a button to install or reinstall WP-CLI. Once installed, you can run WP-CLI using:

If WP-CLI is already installed globally, this plugin does nothing automatically on activation, but you can still use the install page to fetch the bundled copy.

## Running the ReactPHP Server

The plugin can start a simple ReactPHP HTTP server when executed from the command line. For example, using WP‑CLI:

```bash
MCP_WP_FLEX_SERVER=1 wp eval-file wp-content/plugins/mcp-wp-flex/mcp-wp-flex.php
```

The server listens on `http://0.0.0.0:8080` and currently returns a JSON response. Integration with the MCP server and Claude AI should be implemented inside `MCP_WP_Flex::start_server()`.
## WP-CLI Installer Plugin

The **WP-CLI Installer** plugin downloads the `wp-cli.phar` file into its own directory on activation. Once activated, you can run WP-CLI using:

```bash
php wp-content/plugins/wp-cli-installer/wp-cli.phar --info
```

If WP-CLI is already installed globally, this plugin does nothing on activation.
