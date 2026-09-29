<?php
/**
 * Remove plugin data on uninstall.
 *
 * @package Wp_Mortgage_Calculator
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'wpmc_settings' );
delete_option( 'widget_wpmc_widget' );
