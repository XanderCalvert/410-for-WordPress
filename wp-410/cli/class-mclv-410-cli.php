<?php
/**
 * WP-CLI commands for HTTP 410 (Gone) responses plugin.
 *
 * @package MCLV_410_Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only proceed if we're in WP-CLI context and the WP_CLI class is available.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! class_exists( 'WP_CLI' ) ) {
	return;
}

// Ensure the main plugin class exists before defining CLI commands.
if ( class_exists( 'MCLV_410_Plugin' ) ) {

	/**
	 * WP-CLI commands for HTTP 410 (Gone) responses plugin.
	 *
	 * @package MCLV_410_Plugin
	 */
	class MCLV_410_CLI {

		/**
		 * Magic method to handle 'list' command (list is a PHP reserved keyword).
		 *
		 * @param string $method Method name.
		 * @param array  $args   Method arguments.
		 * @return mixed
		 */
		public function __call( $method, $args ) {
			if ( 'list' === $method ) {
				return call_user_func_array( array( $this, 'show' ), $args );
			}
		}

		/**
		 * Seed test 410 and wildcard entries for automated curl tests.
		 *
		 * ## EXAMPLES
		 * wp mclv-410 seed-test-data
		 *
		 * @return void
		 */
		public function seed_test_data() {
			$plugin = new MCLV_410_Plugin();

			$seed = array(
				home_url( '/test-410-deleted-page/' ),
				home_url( '/test-section/deleted-item/' ),
				home_url( '/*/test-410-wildcard/' ),
			);

			foreach ( $seed as $url ) {
				$plugin->add_link( $url );
			}

			WP_CLI::success( 'Seed test data added.' );
		}

		/**
		 * Remove the seeded URLs (does not delete user data).
		 *
		 * ## EXAMPLES
		 * wp mclv-410 clear-test-data
		 *
		 * @return void
		 */
		public function clear_test_data() {
			$plugin = new MCLV_410_Plugin();

			$remove = array(
				home_url( '/test-410-deleted-page/' ),
				home_url( '/test-section/deleted-item/' ),
				home_url( '/*/test-410-wildcard/' ),
			);

			foreach ( $remove as $url ) {
				$plugin->remove_link( $url );
			}

			WP_CLI::success( 'Seeded test data removed.' );
		}

		/**
		 * List all stored 410 and 404 entries.
		 *
		 * ## EXAMPLES
		 * wp mclv-410 list
		 * wp mclv-410 show
		 *
		 * @param array $args       Positional arguments. Unused but required by WP-CLI interface.
		 * @param array $assoc_args Associative arguments. Unused but required by WP-CLI interface.
		 * @return void
		 */
		public function show( $args, $assoc_args ) {
			// phpcs:ignore WordPress.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by WP-CLI interface.
			unset( $args, $assoc_args );
			$plugin      = new MCLV_410_Plugin();
			$links       = $plugin->get_links();
			$logged_404s = $plugin->get_404s();

			WP_CLI::line( '' );
			WP_CLI::line( '=== 410 Entries ===' );
			if ( empty( $links ) ) {
				WP_CLI::line( 'No 410 entries found.' );
			} else {
				foreach ( array_keys( $links ) as $url ) {
					$is_wildcard = ( false !== strpos( $url, '*' ) ) ? ' (wildcard)' : '';
					WP_CLI::line( '  ' . $url . $is_wildcard );
				}
				WP_CLI::line( 'Total: ' . count( $links ) . ' entries' );
			}

			WP_CLI::line( '' );
			WP_CLI::line( '=== 404 Entries ===' );
			if ( empty( $logged_404s ) ) {
				WP_CLI::line( 'No 404 entries found.' );
			} else {
				foreach ( array_keys( $logged_404s ) as $url ) {
					WP_CLI::line( '  ' . $url );
				}
				WP_CLI::line( 'Total: ' . count( $logged_404s ) . ' entries' );
			}
			WP_CLI::line( '' );
		}

		/**
		 * Add a manual 410 rule from CLI.
		 *
		 * ## OPTIONS
		 * <url>
		 * : The URL to add (supports wildcards with *)
		 *
		 * ## EXAMPLES
		 * wp mclv-410 add "http://example.com/deleted-page/"
		 * wp mclv-410 add "http://example.com/any-path/old-section/"
		 *
		 * Note: Wildcards are supported using the asterisk character.
		 *
		 * @param array $args Command arguments.
		 * @return void
		 */
		public function add( $args ) {
			if ( empty( $args[0] ) ) {
				WP_CLI::error( 'Please provide a URL to add.' );
			}

			$url    = $args[0];
			$plugin = new MCLV_410_Plugin();

			// Validate URL.
			if ( ! $plugin->is_valid_url( $url ) ) {
				WP_CLI::warning( 'URL may not be valid for this WordPress installation, but adding anyway.' );
			}

			$result = $plugin->add_link( $url );
			if ( 0 === $result ) {
				WP_CLI::warning( 'URL already exists in the list.' );
			} else {
				WP_CLI::success( 'URL added: ' . $url );
			}
		}

		/**
		 * Clear all logged 404 entries.
		 *
		 * ## EXAMPLES
		 * wp mclv-410 purge-404s
		 *
		 * @return void
		 */
		public function purge_404s() {
			$plugin = new MCLV_410_Plugin();

			$deleted = $plugin->purge_404s();
			WP_CLI::success( 'Purged ' . $deleted . ' 404 entries.' );
		}

		/**
		 * All-in-one test command: seeds, lists, then cleans up.
		 *
		 * ## EXAMPLES
		 * wp mclv-410 test
		 *
		 * @return void
		 */
		public function test() {
			WP_CLI::line( '' );
			WP_CLI::line( '=== Seeding test data ===' );
			$this->seed_test_data();

			WP_CLI::line( '' );
			WP_CLI::line( '=== Current entries ===' );
			$this->show( array(), array() );

			WP_CLI::line( '' );
			WP_CLI::line( '=== Cleaning up test data ===' );
			$this->clear_test_data();

			WP_CLI::line( '' );
			WP_CLI::success( 'Test completed!' );
		}

		/**
		 * Internal helper: get HTTP status code for a URL using cURL.
		 *
		 * @param string $url URL to request.
		 * @return int HTTP status code (0 if request failed).
		 */
		private function get_status_code( $url ) {
			if ( ! function_exists( 'curl_init' ) ) {
				WP_CLI::warning( 'cURL is not available in this PHP environment.' );
				return 0;
			}

			// Need HEAD requests and specific cURL options for CLI testing that wp_remote_get() cannot provide.
			// phpcs:disable WordPress.WP.AlternativeFunctions
			$ch = curl_init( $url );

			curl_setopt( $ch, CURLOPT_NOBODY, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );

			curl_exec( $ch );
			$status = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
			curl_close( $ch );
			// phpcs:enable WordPress.WP.AlternativeFunctions

			return (int) $status;
		}

		/**
		 * Developer-only HTTP self-test.
		 *
		 * Seeds some 410 rules, performs HTTP requests against them,
		 * prints only status codes, then cleans up.
		 *
		 * ## EXAMPLES
		 * wp mclv-410 dev-test
		 *
		 * @param array $args       Positional arguments. Unused but required by WP-CLI interface.
		 * @param array $assoc_args Associative arguments. Unused but required by WP-CLI interface.
		 * @return void
		 */
		public function dev_test( $args, $assoc_args ) {
			// phpcs:ignore WordPress.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required by WP-CLI interface.
			unset( $args, $assoc_args );
			$base_410     = home_url( '/test-410-deleted-page/' );
			$base_410_alt = home_url( '/test-section/deleted-item/' );

			WP_CLI::line( '' );
			WP_CLI::line( '=== Seeding test data ===' );
			$this->seed_test_data();

			$urls = array(
				$base_410,
				$base_410_alt,
				home_url( '/this-should-404-mclv-410-test/' ),
			);

			WP_CLI::line( '' );
			WP_CLI::line( '=== HTTP status checks ===' );

			foreach ( $urls as $url ) {
				$status = $this->get_status_code( $url );

				// Colourise output a bit for quick scanning.
				if ( 410 === $status ) {
					$status_label = WP_CLI::colorize( '%G410%n' );
				} elseif ( 404 === $status ) {
					$status_label = WP_CLI::colorize( '%Y404%n' );
				} elseif ( 0 === $status ) {
					$status_label = WP_CLI::colorize( '%R0 (request failed)%n' );
				} else {
					$status_label = WP_CLI::colorize( '%R' . $status . '%n' );
				}

				WP_CLI::line( '  ' . $url . ' → ' . $status_label );
			}

			WP_CLI::line( '' );
			WP_CLI::line( '=== Cleaning up test data ===' );
			$this->clear_test_data();

			WP_CLI::line( '' );
			WP_CLI::success( 'Developer HTTP test completed.' );
		}
	}

	// Register the command.
	WP_CLI::add_command( 'mclv-410', 'MCLV_410_CLI' );
}
