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

require_once WPMC_DIR . 'includes/settings.php';
require_once WPMC_DIR . 'includes/frontend.php';
require_once WPMC_DIR . 'includes/class-wpmc-widget.php';

if ( is_admin() ) {
	require_once WPMC_DIR . 'admin/admin.php';
	require_once WPMC_DIR . 'admin/help.php';
}
