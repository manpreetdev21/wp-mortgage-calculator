<?php
/**
 * Front end: calculator markup, shortcode and assets.
 *
 * @package Wp_Mortgage_Calculator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Calculator markup, shared by the shortcode and the widget.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function wpmc_render( $atts = array() ) {
	static $n = 0;
	$n++;
	$o = wpmc_options();
	$a = shortcode_atts(
		array(
			'title'                 => __( 'Monthly Mortgage Payments', 'wp-mortgage-calculator' ),
			'total_amount_label'    => __( 'Total Amount', 'wp-mortgage-calculator' ),
			'down_payment_label'    => __( 'Down Payment', 'wp-mortgage-calculator' ),
			'interest_rate_label'   => __( 'Interest Rate', 'wp-mortgage-calculator' ),
			'mortgage_period_label' => __( 'Mortgage Period', 'wp-mortgage-calculator' ),
			'button_label'          => __( 'Calculate Mortgage', 'wp-mortgage-calculator' ),
			'total_amount'          => $o['total_amount'],
			'down_payment'          => $o['down_payment'],
			'interest_rate'         => $o['interest_rate'],
			'mortgage_period'       => $o['mortgage_period'],
		),
		$atts,
		'wp_mortgage_calculator'
	);

	wp_enqueue_style( 'wpmc' );
	wp_enqueue_script( 'wpmc' );

	$inputs = array(
		'total_amount'    => array( $a['total_amount_label'], wpmc_symbol(), 1, 'any', 1000000000000 ),
		'down_payment'    => array( $a['down_payment_label'], wpmc_symbol(), 0, 'any', 1000000000000 ),
		'interest_rate'   => array( $a['interest_rate_label'], '%', 0, 'any', 100 ),
		'mortgage_period' => array( $a['mortgage_period_label'], __( 'Years', 'wp-mortgage-calculator' ), 1, 1, 50 ),
	);

	ob_start();
	?>
	<div class="wpmc">
		<?php if ( '' !== $a['title'] ) : ?>
			<h3 class="wpmc-title"><?php echo esc_html( $a['title'] ); ?></h3>
		<?php endif; ?>
		<form class="wpmc-form">
			<?php foreach ( $inputs as $name => $in ) : $id = "wpmc-$n-$name"; ?>
				<p>
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $in[0] ); ?></label>
					<input type="number" required id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"
						min="<?php echo esc_attr( $in[2] ); ?>" step="<?php echo esc_attr( $in[3] ); ?>" max="<?php echo esc_attr( $in[4] ); ?>"
						placeholder="<?php echo esc_attr( $in[1] ); ?>" value="<?php echo esc_attr( $a[ $name ] ); ?>">
				</p>
			<?php endforeach; ?>
			<p><button type="submit"><?php echo esc_html( $a['button_label'] ); ?></button></p>
		</form>
		<div class="wpmc-output" hidden aria-live="polite">
			<ul class="wpmc-results"></ul>
			<button type="button" class="wpmc-schedule-btn"><?php esc_html_e( 'View Amortization Schedule', 'wp-mortgage-calculator' ); ?></button>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wp_mortgage_calculator', 'wpmc_render' );

/**
 * Register front-end assets (enqueued only when a calculator renders).
 */
function wpmc_register_assets() {
	$o = wpmc_options();
	wp_register_style( 'wpmc', WPMC_URL . 'assets/css/wpmc.css', array(), WPMC_VERSION );
	wp_register_script( 'wpmc-chartjs', WPMC_URL . 'assets/vendor/chart.umd.min.js', array(), '4.5.1', true );
	wp_register_script( 'wpmc', WPMC_URL . 'assets/js/wpmc.js', array( 'jquery', 'wpmc-chartjs' ), WPMC_VERSION, true );
	wp_localize_script(
		'wpmc',
		'wpmc',
		array(
			'currency' => wpmc_symbol(),
			'position' => $o['position'],
			'thousand' => $o['thousand'],
			'decimal'  => $o['decimal'],
			'decimals' => min( absint( $o['decimals'] ), 6 ),
			'labels'   => array(
				'principal'      => $o['label_principal'],
				'years'          => $o['label_years'],
				'monthly'        => $o['label_monthly'],
				'total_interest' => $o['label_total_interest'],
				'total_down'     => $o['label_total_down'],
			),
			'i18n'     => array(
				'eyebrow'            => __( 'Mortgage breakdown', 'wp-mortgage-calculator' ),
				'schedule_title'     => __( 'Amortization Schedule', 'wp-mortgage-calculator' ),
				'close'              => __( 'Close', 'wp-mortgage-calculator' ),
				'monthly'            => __( 'Monthly payment', 'wp-mortgage-calculator' ),
				'years'              => __( 'years', 'wp-mortgage-calculator' ),
				'principal_interest' => __( 'Principal & interest', 'wp-mortgage-calculator' ),
				'loan'               => __( 'Loan amount', 'wp-mortgage-calculator' ),
				'down_payment'       => __( 'Down payment', 'wp-mortgage-calculator' ),
				'total_interest'     => __( 'Total interest', 'wp-mortgage-calculator' ),
				'over_time'          => __( 'Loan balance over time', 'wp-mortgage-calculator' ),
				'balance'            => __( 'Balance', 'wp-mortgage-calculator' ),
				'principal_paid'     => __( 'Principal paid', 'wp-mortgage-calculator' ),
				'interest_paid'      => __( 'Interest paid', 'wp-mortgage-calculator' ),
				/* translators: %s: number of payments */
				'total_of'           => __( 'Total of %s payments', 'wp-mortgage-calculator' ),
				'principal'          => __( 'Principal', 'wp-mortgage-calculator' ),
				'interest'           => __( 'Interest', 'wp-mortgage-calculator' ),
				'total_cost'         => __( 'Total cost', 'wp-mortgage-calculator' ),
				/* translators: %s: interest rate */
				'compare'            => __( '15- vs 30-year term at %s%', 'wp-mortgage-calculator' ),
				'year_term'          => __( 'year term', 'wp-mortgage-calculator' ),
				'per_month'          => __( '/mo', 'wp-mortgage-calculator' ),
				'schedule'           => __( 'Payment schedule', 'wp-mortgage-calculator' ),
				'yearly'             => __( 'Yearly', 'wp-mortgage-calculator' ),
				'monthly_view'       => __( 'Monthly', 'wp-mortgage-calculator' ),
				'year'               => __( 'Year', 'wp-mortgage-calculator' ),
				'month'              => __( 'Month', 'wp-mortgage-calculator' ),
				'payment'            => __( 'Payment', 'wp-mortgage-calculator' ),
				'total_paid'         => __( 'Total paid', 'wp-mortgage-calculator' ),
				'down_error'         => __( 'Down payment must be less than the total amount.', 'wp-mortgage-calculator' ),
			),
		)
	);

	// Re-validate on output: these values land inside a <style> tag.
	$sizes    = wpmc_button_sizes();
	$size     = isset( $sizes[ $o['btn_size'] ] ) ? $sizes[ $o['btn_size'] ] : $sizes['medium'];
	$defaults = wpmc_defaults();
	$color    = function ( $key ) use ( $o, $defaults ) {
		return sanitize_hex_color( $o[ $key ] ) ? sanitize_hex_color( $o[ $key ] ) : $defaults[ $key ];
	};
	wp_add_inline_style(
		'wpmc',
		sprintf(
			'.wpmc,.wpmc-dialog{--wpmc-btn-bg:%s;--wpmc-btn-text:%s;--wpmc-btn-hover:%s;--wpmc-btn-pad:%s;--wpmc-btn-font:%s;--wpmc-primary:%s}',
			$color( 'btn_bg' ), $color( 'btn_text' ), $color( 'btn_hover' ), $size[1], $size[2], $color( 'accent' )
		)
	);
}
add_action( 'init', 'wpmc_register_assets' ); // init, not wp_enqueue_scripts: block themes render shortcodes earlier.
