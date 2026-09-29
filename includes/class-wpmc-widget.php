<?php
/**
 * Calculator widget.
 *
 * @package Wp_Mortgage_Calculator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget.
 */
class WPMC_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wpmc_widget',
			__( 'Wp Mortgage Calculator', 'wp-mortgage-calculator' ),
			array( 'description' => __( 'A mortgage calculator with amortization schedule.', 'wp-mortgage-calculator' ) )
		);
	}

	private function fields() {
		return array(
			'title'                 => __( 'Title', 'wp-mortgage-calculator' ),
			'total_amount_label'    => __( 'Total Amount Label', 'wp-mortgage-calculator' ),
			'down_payment_label'    => __( 'Down Payment Label', 'wp-mortgage-calculator' ),
			'interest_rate_label'   => __( 'Interest Rate Label', 'wp-mortgage-calculator' ),
			'mortgage_period_label' => __( 'Mortgage Period Label', 'wp-mortgage-calculator' ),
		);
	}

	public function widget( $args, $instance ) {
		$instance = array_filter( (array) $instance ); // Empty fields fall back to defaults.
		$title    = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : __( 'Monthly Mortgage Payments', 'wp-mortgage-calculator' ) );

		echo wp_kses_post( $args['before_widget'] );
		if ( $title ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		echo wpmc_render( array_merge( $instance, array( 'title' => '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every value is escaped inside wpmc_render().
		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		foreach ( $this->fields() as $key => $label ) {
			printf(
				'<p><label for="%1$s">%2$s:</label><input class="widefat" type="text" id="%1$s" name="%3$s" value="%4$s"></p>',
				esc_attr( $this->get_field_id( $key ) ),
				esc_html( $label ),
				esc_attr( $this->get_field_name( $key ) ),
				esc_attr( isset( $instance[ $key ] ) ? $instance[ $key ] : '' )
			);
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		foreach ( array_keys( $this->fields() ) as $key ) {
			$instance[ $key ] = isset( $new_instance[ $key ] ) ? sanitize_text_field( $new_instance[ $key ] ) : '';
		}
		return $instance;
	}
}
add_action( 'widgets_init', function () {
	register_widget( 'WPMC_Widget' );
} );
