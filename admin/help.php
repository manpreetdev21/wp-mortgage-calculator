<?php
/**
 * Admin: Help tab documentation.
 *
 * @package Wp_Mortgage_Calculator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Code block with a copy button.
 *
 * @param string $code Code to show.
 */
function wpmc_code( $code ) {
	printf(
		'<div class="wpmc-a-code"><code>%1$s</code><button type="button" class="wpmc-a-copy" data-copy="%2$s">%3$s</button></div>',
		esc_html( $code ),
		esc_attr( $code ),
		esc_html__( 'Copy', 'wp-mortgage-calculator' )
	);
}

/**
 * Help tab documentation.
 */
function wpmc_help() {
	$steps = array(
		array( 'admin-generic', __( 'Configure', 'wp-mortgage-calculator' ), __( 'Pick your currency, button style and default values under Settings.', 'wp-mortgage-calculator' ) ),
		array( 'shortcode', __( 'Embed', 'wp-mortgage-calculator' ), __( 'Add the shortcode to any page or post, or use the widget in a sidebar.', 'wp-mortgage-calculator' ) ),
		array( 'chart-area', __( 'Calculate', 'wp-mortgage-calculator' ), __( 'Visitors get instant results and a detailed popup with charts and a full schedule.', 'wp-mortgage-calculator' ) ),
	);
	$atts  = array(
		'title'                 => __( 'Heading above the calculator. Use title="" to hide it.', 'wp-mortgage-calculator' ),
		'total_amount_label'    => __( 'Label of the Total Amount field.', 'wp-mortgage-calculator' ),
		'down_payment_label'    => __( 'Label of the Down Payment field.', 'wp-mortgage-calculator' ),
		'interest_rate_label'   => __( 'Label of the Interest Rate field.', 'wp-mortgage-calculator' ),
		'mortgage_period_label' => __( 'Label of the Mortgage Period field.', 'wp-mortgage-calculator' ),
		'button_label'          => __( 'Text of the calculate button.', 'wp-mortgage-calculator' ),
		'total_amount'          => __( 'Pre-filled total amount (overrides Default Values).', 'wp-mortgage-calculator' ),
		'down_payment'          => __( 'Pre-filled down payment.', 'wp-mortgage-calculator' ),
		'interest_rate'         => __( 'Pre-filled interest rate in %.', 'wp-mortgage-calculator' ),
		'mortgage_period'       => __( 'Pre-filled mortgage term in years.', 'wp-mortgage-calculator' ),
	);
	?>
	<div class="wpmc-a-help">
		<div class="wpmc-a-steps">
			<?php foreach ( $steps as $i => $step ) : ?>
				<div class="wpmc-a-card wpmc-a-step">
					<span class="wpmc-a-icon dashicons dashicons-<?php echo esc_attr( $step[0] ); ?>" aria-hidden="true"></span>
					<small><?php echo esc_html( sprintf( /* translators: %d: step number */ __( 'Step %d', 'wp-mortgage-calculator' ), $i + 1 ) ); ?></small>
					<h3><?php echo esc_html( $step[1] ); ?></h3>
					<p><?php echo esc_html( $step[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>

		<section class="wpmc-a-card">
			<h2><?php esc_html_e( 'Shortcode', 'wp-mortgage-calculator' ); ?></h2>
			<p><?php esc_html_e( 'Paste this into any post, page or Shortcode block:', 'wp-mortgage-calculator' ); ?></p>
			<?php wpmc_code( '[wp_mortgage_calculator]' ); ?>
			<p><?php esc_html_e( 'Example with a custom title and pre-filled values:', 'wp-mortgage-calculator' ); ?></p>
			<?php wpmc_code( '[wp_mortgage_calculator title="Home Loan Calculator" total_amount="350000" down_payment="70000" interest_rate="6.5" mortgage_period="30"]' ); ?>

			<h3><?php esc_html_e( 'Attributes', 'wp-mortgage-calculator' ); ?></h3>
			<table class="wpmc-a-table">
				<thead><tr><th><?php esc_html_e( 'Attribute', 'wp-mortgage-calculator' ); ?></th><th><?php esc_html_e( 'Description', 'wp-mortgage-calculator' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( $atts as $att => $desc ) : ?>
					<tr><td><code><?php echo esc_html( $att ); ?></code></td><td><?php echo esc_html( $desc ); ?></td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</section>

		<div class="wpmc-a-grid">
			<section class="wpmc-a-card">
				<h2><span class="dashicons dashicons-welcome-widgets-menus" aria-hidden="true"></span><?php esc_html_e( 'Widget', 'wp-mortgage-calculator' ); ?></h2>
				<p><?php esc_html_e( 'Go to Appearance → Widgets, add the "Wp Mortgage Calculator" widget (or the Legacy Widget block) to a sidebar, and optionally change the title and field labels.', 'wp-mortgage-calculator' ); ?></p>
			</section>
			<section class="wpmc-a-card">
				<h2><span class="dashicons dashicons-editor-code" aria-hidden="true"></span><?php esc_html_e( 'Theme template', 'wp-mortgage-calculator' ); ?></h2>
				<p><?php esc_html_e( 'Output the calculator anywhere in a PHP template:', 'wp-mortgage-calculator' ); ?></p>
				<?php wpmc_code( "<?php echo do_shortcode( '[wp_mortgage_calculator]' ); ?>" ); ?>
			</section>
			<section class="wpmc-a-card">
				<h2><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php esc_html_e( 'Settings', 'wp-mortgage-calculator' ); ?></h2>
				<ul>
					<li><strong><?php esc_html_e( 'Price Format', 'wp-mortgage-calculator' ); ?></strong> — <?php esc_html_e( 'currency, sign position, separators and decimals.', 'wp-mortgage-calculator' ); ?></li>
					<li><strong><?php esc_html_e( 'Appearance', 'wp-mortgage-calculator' ); ?></strong> — <?php esc_html_e( 'button colors, hover color, size and the popup accent color.', 'wp-mortgage-calculator' ); ?></li>
					<li><strong><?php esc_html_e( 'Result Labels', 'wp-mortgage-calculator' ); ?></strong> — <?php esc_html_e( 'text of each result line; empty hides the line.', 'wp-mortgage-calculator' ); ?></li>
					<li><strong><?php esc_html_e( 'Default Values', 'wp-mortgage-calculator' ); ?></strong> — <?php esc_html_e( 'pre-filled values; shortcode attributes override them.', 'wp-mortgage-calculator' ); ?></li>
				</ul>
			</section>
			<section class="wpmc-a-card">
				<h2><span class="dashicons dashicons-chart-pie" aria-hidden="true"></span><?php esc_html_e( 'Amortization popup', 'wp-mortgage-calculator' ); ?></h2>
				<p><?php esc_html_e( 'After calculating, "View Amortization Schedule" opens a popup with the monthly payment, a loan-balance chart, a cost breakdown chart, a 15- vs 30-year comparison and a yearly/monthly schedule. Charts use the bundled Chart.js library; no third-party service is called and no data leaves your site.', 'wp-mortgage-calculator' ); ?></p>
			</section>
			<section class="wpmc-a-card">
				<h2><span class="dashicons dashicons-calculator" aria-hidden="true"></span><?php esc_html_e( 'How it is calculated', 'wp-mortgage-calculator' ); ?></h2>
				<p><?php esc_html_e( 'Loan = Total Amount − Down Payment. Monthly payment M = P × r / (1 − (1 + r)^−n), where r is the monthly rate and n the number of months. At 0% the loan is divided evenly over the months.', 'wp-mortgage-calculator' ); ?></p>
			</section>
			<section class="wpmc-a-card">
				<h2><span class="dashicons dashicons-admin-customizer" aria-hidden="true"></span><?php esc_html_e( 'Developers', 'wp-mortgage-calculator' ); ?></h2>
				<p><?php esc_html_e( 'Chart colors can be changed with CSS variables in your theme:', 'wp-mortgage-calculator' ); ?></p>
				<?php wpmc_code( '.wpmc-dialog { --wpmc-interest: #f59e0b; --wpmc-down: #10b981; }' ); ?>
				<p><?php esc_html_e( 'Add currencies with the wpmc_currencies filter:', 'wp-mortgage-calculator' ); ?></p>
				<?php wpmc_code( "add_filter( 'wpmc_currencies', function ( \$c ) { \$c['XOF'] = array( 'West African CFA', 'CFA' ); return \$c; } );" ); ?>
			</section>
		</div>
	</div>
	<?php
}
