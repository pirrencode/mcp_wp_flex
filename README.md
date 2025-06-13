# MCP WP Flex

WordPress plugin that demonstrates how to integrate with the [MCP server](https://github.com/php-mcp/server) using [ReactPHP](https://reactphp.org/).

## Installation

1. Install PHP dependencies using Composer:

```bash
composer install
```

2. Copy the `mcp-wp-flex` directory into your WordPress `plugins` folder.

3. Activate **MCP WP Flex** from the WordPress admin.

## Running the ReactPHP Server

The plugin can start a simple ReactPHP HTTP server when executed from the command line. For example, using WP‑CLI:

```bash
MCP_WP_FLEX_SERVER=1 wp eval-file wp-content/plugins/mcp-wp-flex/mcp-wp-flex.php
```

The server listens on `http://0.0.0.0:8080` and currently returns a JSON response. Integration with the MCP server and Claude AI should be implemented inside `MCP_WP_Flex::start_server()`.
