# HTTP 410 (Gone) Responses

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/wp-410?logo=wordpress&logoColor=white)](https://wordpress.org/plugins/wp-410/)
[![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/wp-410?logo=wordpress&logoColor=white)](https://wordpress.org/plugins/wp-410/)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A WordPress plugin that sends HTTP 410 (Gone) responses for deleted content, telling search engines the page is permanently removed.

## Description

This plugin issues an HTTP `410` response for URLs corresponding to content that has been permanently removed from your site. When a post or page is deleted, the plugin logs the old URL and returns a `410` response when that URL is requested. You can also manually manage the list of obsolete URLs.

The [HTTP Specification](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.11) defines the `410 Gone` response for resources that have been permanently removed. It informs search engines and crawlers that the content will not return, improving crawl efficiency and SEO clarity.

## Installation

1. Upload the `wp-410` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the **Plugins** menu in WordPress
3. Access the plugin settings via **Plugins → HTTP 410 (Gone) responses**

Or install directly from the [WordPress Plugin Directory](https://wordpress.org/plugins/wp-410/).

## Features

- 🔗 Automatically track deleted content URLs
- 📋 Log 404 errors and easily convert them to 410 responses
- ✨ Wildcard URL support (e.g., `https://example.com/*/old-section/`)
- 🎨 Custom 410 template support via `410.php` in your theme
- 🪝 Developer hook: `mclv_410_response` action
- ✅ Compatible with W3 Total Cache and WP Super Cache
- 🖥️ **WP-CLI support** - Manage 410 URLs from the command line

## Customisation

### Custom Template

Place a `410.php` file in your theme folder to customise the 410 response page. Use your theme's `404.php` as a reference.

### Action Hook

```php
add_action( 'mclv_410_response', function() {
    // Your custom logic when a 410 response is triggered
});
```

## WP-CLI Commands

Manage your 410 URLs directly from the command line:

```bash
# List all 410 and 404 entries
wp mclv-410 list
# or
wp mclv-410 show

# Add a URL manually (supports wildcards with *)
wp mclv-410 add "https://example.com/deleted-page/"
wp mclv-410 add "https://example.com/*/old-section/"

# Clear all logged 404 entries
wp mclv-410 purge-404s

# Development/testing commands
wp mclv-410 seed-test-data    # Add test data
wp mclv-410 clear-test-data   # Remove test data
wp mclv-410 test              # All-in-one test (seed, list, cleanup)
wp mclv-410 dev-test          # HTTP self-test with actual requests
```

### Example Usage

```bash
# Check what URLs are currently configured
wp mclv-410 list

# Add a deleted page
wp mclv-410 add "https://yoursite.com/old-blog-post/"

# Add a wildcard pattern for an entire section
wp mclv-410 add "https://yoursite.com/old-category/*/"

# Clear all 404 logs
wp mclv-410 purge-404s

# Run a developer test to verify 410 responses are working
wp mclv-410 dev-test
```

## Caching Plugin Compatibility

Tested and working with:
- W3 Total Cache
- WP Super Cache

> ⚠️ Other caching plugins may cache responses as 404s instead of 410s.

## Development

```bash
# Install dependencies
composer install

# Run PHP CodeSniffer
composer lint

# Auto-fix coding standards
composer lint-fix
```

## Roadmap / Wishlist

The following features are being considered for future releases. **Community interest and pull requests are welcome!** If you'd like to see a feature implemented faster, feel free to open an issue or submit a PR.

### Potential Features

- 📊 **Analytics Dashboard** - Track 410 hits, referrers, and trends over time
- 📥 **CSV Import/Export** - Bulk import URLs from CSV or export your current list
- 🔍 **Search & Filter** - Search and filter URLs in the admin interface
- 🔌 **REST API Endpoints** - Programmatic access to manage URLs via API
- 💡 **Smart Suggestions** - Auto-suggest URLs from 404 logs that match patterns
- 🔗 **Plugin Integrations** - Integration with popular SEO and redirect plugins
- ⚡ **Performance Optimizations** - Caching and query optimizations for large URL lists

### Contributing

Pull requests are welcome! If you're interested in implementing any of these features or have ideas for improvements, please:

1. Open an issue to discuss the feature
2. Fork the repository
3. Create a feature branch
4. Submit a pull request

See the [Development](#development) section for setup instructions.

## Credits

Originally created by [Samir Shah](http://rayofsolaris.net/) ([@solarissmoke](https://profiles.wordpress.org/solarissmoke/)).

Now maintained by [Matt Calvert](https://calvert.media).

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

