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

## Customisation

### Custom Template

Place a `410.php` file in your theme folder to customise the 410 response page. Use your theme's `404.php` as a reference.

### Action Hook

```php
add_action( 'mclv_410_response', function() {
    // Your custom logic when a 410 response is triggered
});
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

## Credits

Originally created by [Samir Shah](http://rayofsolaris.net/) ([@solarissmoke](https://profiles.wordpress.org/solarissmoke/)).

Now maintained by [Matt Calvert](https://calvert.media).

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

