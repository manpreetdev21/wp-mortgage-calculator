<?php
/**
 * Settings data: defaults, saved options, currencies, fields and button sizes.
 *
 * @package Wp_Mortgage_Calculator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default settings.
 */
function wpmc_defaults() {
	return array(
		'currency'             => 'USD',
		'position'             => 'before',
		'thousand'             => ',',
		'decimal'              => '.',
		'decimals'             => 2,
		'label_principal'      => __( 'Principal Amount:', 'wp-mortgage-calculator' ),
		'label_years'          => __( 'Years:', 'wp-mortgage-calculator' ),
		'label_monthly'        => __( 'Monthly Payment:', 'wp-mortgage-calculator' ),
		'label_total_interest' => __( 'Balance Payable With Interest:', 'wp-mortgage-calculator' ),
		'label_total_down'     => __( 'Total With Down Payment:', 'wp-mortgage-calculator' ),
		'total_amount'         => '',
		'down_payment'         => '',
		'interest_rate'        => '',
		'mortgage_period'      => '',
		'btn_bg'               => '#4f46e5',
		'btn_text'             => '#ffffff',
		'btn_hover'            => '#4338ca',
		'btn_size'             => 'medium',
		'accent'               => '#4f46e5',
	);
}

/**
 * Saved settings merged over defaults.
 */
function wpmc_options() {
	return wp_parse_args( (array) get_option( 'wpmc_settings', array() ), wpmc_defaults() );
}

/**
 * Supported currencies: ISO code => array( name, symbol ).
 * Add more with the 'wpmc_currencies' filter.
 */
function wpmc_currencies() {
	return apply_filters(
		'wpmc_currencies',
		array(
			'USD' => array( __( 'US Dollar', 'wp-mortgage-calculator' ), '$' ),
			'EUR' => array( __( 'Euro', 'wp-mortgage-calculator' ), '€' ),
			'GBP' => array( __( 'British Pound', 'wp-mortgage-calculator' ), '£' ),
			'INR' => array( __( 'Indian Rupee', 'wp-mortgage-calculator' ), '₹' ),
			'CAD' => array( __( 'Canadian Dollar', 'wp-mortgage-calculator' ), 'C$' ),
			'AUD' => array( __( 'Australian Dollar', 'wp-mortgage-calculator' ), 'A$' ),
			'NZD' => array( __( 'New Zealand Dollar', 'wp-mortgage-calculator' ), 'NZ$' ),
			'JPY' => array( __( 'Japanese Yen', 'wp-mortgage-calculator' ), '¥' ),
			'CNY' => array( __( 'Chinese Yuan', 'wp-mortgage-calculator' ), '¥' ),
			'HKD' => array( __( 'Hong Kong Dollar', 'wp-mortgage-calculator' ), 'HK$' ),
			'SGD' => array( __( 'Singapore Dollar', 'wp-mortgage-calculator' ), 'S$' ),
			'CHF' => array( __( 'Swiss Franc', 'wp-mortgage-calculator' ), 'CHF' ),
			'SEK' => array( __( 'Swedish Krona', 'wp-mortgage-calculator' ), 'kr' ),
			'NOK' => array( __( 'Norwegian Krone', 'wp-mortgage-calculator' ), 'kr' ),
			'DKK' => array( __( 'Danish Krone', 'wp-mortgage-calculator' ), 'kr' ),
			'ISK' => array( __( 'Icelandic Króna', 'wp-mortgage-calculator' ), 'kr' ),
			'PLN' => array( __( 'Polish Złoty', 'wp-mortgage-calculator' ), 'zł' ),
			'CZK' => array( __( 'Czech Koruna', 'wp-mortgage-calculator' ), 'Kč' ),
			'HUF' => array( __( 'Hungarian Forint', 'wp-mortgage-calculator' ), 'Ft' ),
			'RON' => array( __( 'Romanian Leu', 'wp-mortgage-calculator' ), 'lei' ),
			'BGN' => array( __( 'Bulgarian Lev', 'wp-mortgage-calculator' ), 'лв' ),
			'UAH' => array( __( 'Ukrainian Hryvnia', 'wp-mortgage-calculator' ), '₴' ),
			'RUB' => array( __( 'Russian Ruble', 'wp-mortgage-calculator' ), '₽' ),
			'TRY' => array( __( 'Turkish Lira', 'wp-mortgage-calculator' ), '₺' ),
			'ILS' => array( __( 'Israeli New Shekel', 'wp-mortgage-calculator' ), '₪' ),
			'AED' => array( __( 'UAE Dirham', 'wp-mortgage-calculator' ), 'AED' ),
			'SAR' => array( __( 'Saudi Riyal', 'wp-mortgage-calculator' ), 'SAR' ),
			'QAR' => array( __( 'Qatari Riyal', 'wp-mortgage-calculator' ), 'QAR' ),
			'KWD' => array( __( 'Kuwaiti Dinar', 'wp-mortgage-calculator' ), 'KD' ),
			'EGP' => array( __( 'Egyptian Pound', 'wp-mortgage-calculator' ), 'E£' ),
			'ZAR' => array( __( 'South African Rand', 'wp-mortgage-calculator' ), 'R' ),
			'NGN' => array( __( 'Nigerian Naira', 'wp-mortgage-calculator' ), '₦' ),
			'KES' => array( __( 'Kenyan Shilling', 'wp-mortgage-calculator' ), 'KSh' ),
			'GHS' => array( __( 'Ghanaian Cedi', 'wp-mortgage-calculator' ), 'GH₵' ),
			'MAD' => array( __( 'Moroccan Dirham', 'wp-mortgage-calculator' ), 'MAD' ),
			'PKR' => array( __( 'Pakistani Rupee', 'wp-mortgage-calculator' ), '₨' ),
			'BDT' => array( __( 'Bangladeshi Taka', 'wp-mortgage-calculator' ), '৳' ),
			'LKR' => array( __( 'Sri Lankan Rupee', 'wp-mortgage-calculator' ), 'Rs' ),
			'NPR' => array( __( 'Nepalese Rupee', 'wp-mortgage-calculator' ), 'Rs' ),
			'KRW' => array( __( 'South Korean Won', 'wp-mortgage-calculator' ), '₩' ),
			'TWD' => array( __( 'New Taiwan Dollar', 'wp-mortgage-calculator' ), 'NT$' ),
			'THB' => array( __( 'Thai Baht', 'wp-mortgage-calculator' ), '฿' ),
			'MYR' => array( __( 'Malaysian Ringgit', 'wp-mortgage-calculator' ), 'RM' ),
			'IDR' => array( __( 'Indonesian Rupiah', 'wp-mortgage-calculator' ), 'Rp' ),
			'PHP' => array( __( 'Philippine Peso', 'wp-mortgage-calculator' ), '₱' ),
			'VND' => array( __( 'Vietnamese Dong', 'wp-mortgage-calculator' ), '₫' ),
			'MXN' => array( __( 'Mexican Peso', 'wp-mortgage-calculator' ), 'MX$' ),
			'BRL' => array( __( 'Brazilian Real', 'wp-mortgage-calculator' ), 'R$' ),
			'ARS' => array( __( 'Argentine Peso', 'wp-mortgage-calculator' ), 'AR$' ),
			'CLP' => array( __( 'Chilean Peso', 'wp-mortgage-calculator' ), 'CLP$' ),
			'COP' => array( __( 'Colombian Peso', 'wp-mortgage-calculator' ), 'COL$' ),
			'PEN' => array( __( 'Peruvian Sol', 'wp-mortgage-calculator' ), 'S/' ),
		)
	);
}

/**
 * Symbol of the selected currency.
 */
function wpmc_symbol() {
	$list = wpmc_currencies();
	$code = wpmc_options()['currency'];
	return isset( $list[ $code ] ) ? $list[ $code ][1] : '$';
}

/**
 * Settings fields: key => array( label, section, type, description ).
 */
function wpmc_fields() {
	return array(
		'currency'             => array( __( 'Currency', 'wp-mortgage-calculator' ), 'format', 'select', __( 'Default: US Dollar ($)', 'wp-mortgage-calculator' ) ),
		'position'             => array( __( 'Currency Sign Position', 'wp-mortgage-calculator' ), 'format', 'select', '' ),
		'thousand'             => array( __( 'Thousand Separator', 'wp-mortgage-calculator' ), 'format', 'text', __( 'Default: ,', 'wp-mortgage-calculator' ) ),
		'decimal'              => array( __( 'Decimal Separator', 'wp-mortgage-calculator' ), 'format', 'text', __( 'Default: .', 'wp-mortgage-calculator' ) ),
		'decimals'             => array( __( 'Number of Decimals', 'wp-mortgage-calculator' ), 'format', 'number', __( 'Default: 2', 'wp-mortgage-calculator' ) ),
		'label_principal'      => array( __( 'Principal Amount', 'wp-mortgage-calculator' ), 'labels', 'text', '' ),
		'label_years'          => array( __( 'Years', 'wp-mortgage-calculator' ), 'labels', 'text', '' ),
		'label_monthly'        => array( __( 'Monthly Payment', 'wp-mortgage-calculator' ), 'labels', 'text', '' ),
		'label_total_interest' => array( __( 'Balance Payable With Interest', 'wp-mortgage-calculator' ), 'labels', 'text', '' ),
		'label_total_down'     => array( __( 'Total With Down Payment', 'wp-mortgage-calculator' ), 'labels', 'text', '' ),
		'total_amount'         => array( __( 'Total Amount', 'wp-mortgage-calculator' ), 'defaults', 'number', __( 'For example: 300000', 'wp-mortgage-calculator' ) ),
		'down_payment'         => array( __( 'Down Payment', 'wp-mortgage-calculator' ), 'defaults', 'number', __( 'For example: 60000', 'wp-mortgage-calculator' ) ),
		'interest_rate'        => array( __( 'Interest Rate (%)', 'wp-mortgage-calculator' ), 'defaults', 'number', __( 'For example: 4.125', 'wp-mortgage-calculator' ) ),
		'mortgage_period'      => array( __( 'Mortgage Term (years)', 'wp-mortgage-calculator' ), 'defaults', 'number', __( 'For example: 30', 'wp-mortgage-calculator' ) ),
		'btn_bg'               => array( __( 'Button Background', 'wp-mortgage-calculator' ), 'appearance', 'color', '' ),
		'btn_text'             => array( __( 'Button Text Color', 'wp-mortgage-calculator' ), 'appearance', 'color', '' ),
		'btn_hover'            => array( __( 'Button Hover Background', 'wp-mortgage-calculator' ), 'appearance', 'color', '' ),
		'btn_size'             => array( __( 'Button Size', 'wp-mortgage-calculator' ), 'appearance', 'select', '' ),
		'accent'               => array( __( 'Popup Accent Color', 'wp-mortgage-calculator' ), 'appearance', 'color', __( 'Used for the popup highlight and principal in charts.', 'wp-mortgage-calculator' ) ),
	);
}

/**
 * Button sizes: key => array( label, padding, font size ).
 */
function wpmc_button_sizes() {
	return array(
		'small'  => array( __( 'Small', 'wp-mortgage-calculator' ), '8px 16px', '13px' ),
		'medium' => array( __( 'Medium', 'wp-mortgage-calculator' ), '12px 22px', '15px' ),
		'large'  => array( __( 'Large', 'wp-mortgage-calculator' ), '16px 32px', '17px' ),
	);
}
