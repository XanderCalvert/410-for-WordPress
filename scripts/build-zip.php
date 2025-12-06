<?php
/**
 * Build script to create a versioned ZIP of the plugin.
 *
 * Usage: composer zip
 *
 * @package MCLV_410_Plugin
 */

// Change to project root.
chdir( dirname( __DIR__ ) );

// Read version from plugin file.
$plugin_file = file_get_contents( 'wp-410/wp-410.php' );
if ( ! preg_match( '/\*\s*Version:\s*([0-9.]+)/i', $plugin_file, $matches ) ) {
	echo "Error: Could not extract version from wp-410.php\n";
	exit( 1 );
}

$version  = $matches[1];
$zip_name = "wp-410-{$version}.zip";

// Remove old zip if exists.
if ( file_exists( $zip_name ) ) {
	unlink( $zip_name );
}

// Use PowerShell to create ZIP (works on Windows without ZipArchive extension).
$powershell = 'C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe';
$command    = sprintf(
	'%s -NoProfile -Command "Compress-Archive -Path wp-410 -DestinationPath \'%s\' -Force"',
	$powershell,
	$zip_name
);

$output = array();
$result = 0;
exec( $command, $output, $result );

if ( 0 !== $result ) {
	echo "Error: Failed to create ZIP file\n";
	echo implode( "\n", $output ) . "\n";
	exit( 1 );
}

// Verify the file was created.
if ( ! file_exists( $zip_name ) ) {
	echo "Error: ZIP file was not created\n";
	exit( 1 );
}

$size = round( filesize( $zip_name ) / 1024, 1 );

echo "\n";
echo "  Created: {$zip_name}\n";
echo "  Version: {$version}\n";
echo "  Size:    {$size} KB\n";
echo "\n";
