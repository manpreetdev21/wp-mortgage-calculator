<?php
/**
 * Plugin Name: Wp Mortgage Calculator
 * Description: Mortgage calculator widget and shortcode with a built-in amortization schedule popup.
 * Version:     1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author:      Manpreet Singh
 * Author URI:  https://github.com/manpreetdev21/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-mortgage-calculator
 */

defined( 'ABSPATH' ) || exit;

define( 'WPMC_VERSION', '1.0.0' );
define( 'WPMC_FILE', __FILE__ );
define( 'WPMC_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPMC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Asset version from the file's modification time, so browsers and caches
 * fetch the new file after every update. Falls back to the plugin version.
 *
 * @param string $path Path relative to the plugin folder.
 * @return string
 */
function wpmc_asset_ver( $path ) {
	$file = WPMC_DIR . $path;
	return file_exists( $file ) ? (string) filemtime( $file ) : WPMC_VERSION;
}

require_once WPMC_DIR . 'includes/settings.php';
require_once WPMC_DIR . 'includes/frontend.php';
require_once WPMC_DIR . 'includes/class-wpmc-widget.php';

if ( is_admin() ) {
	require_once WPMC_DIR . 'admin/admin.php';
	require_once WPMC_DIR . 'admin/help.php';
}
