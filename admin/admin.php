<?php
/**
 * Admin: menu, settings registration, fields, sanitizing and the settings page.
 *
 * @package Wp_Mortgage_Calculator
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	$title = __( 'Wp Mortgage Calculator', 'wp-mortgage-calculator' );
	add_menu_page( $title, __( 'Mortgage Calculator', 'wp-mortgage-calculator' ), 'manage_options', 'wpmc', 'wpmc_admin_page', 'dashicons-calculator', 80 );
	add_submenu_page( 'wpmc', $title, __( 'Settings', 'wp-mortgage-calculator' ), 'manage_options', 'wpmc', 'wpmc_admin_page' );
	add_submenu_page( 'wpmc', $title, __( 'Help', 'wp-mortgage-calculator' ), 'manage_options', 'wpmc-help', 'wpmc_admin_page' );
} );

add_filter( 'plugin_action_links_' . plugin_basename( WPMC_FILE ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=wpmc' ) ) . '">' . esc_html__( 'Settings', 'wp-mortgage-calculator' ) . '</a>' );
	return $links;
} );

/**
 * Settings sections: id => array( title, description, dashicon ).
 */
function wpmc_sections() {
	return array(
		'format'     => array( __( 'Price Format', 'wp-mortgage-calculator' ), __( 'Choose the currency and how amounts are displayed.', 'wp-mortgage-calculator' ), 'money-alt' ),
		'appearance' => array( __( 'Appearance', 'wp-mortgage-calculator' ), __( 'Colors and size of the calculator buttons, and the popup accent color.', 'wp-mortgage-calculator' ), 'admin-appearance' ),
		'labels'     => array( __( 'Result Labels', 'wp-mortgage-calculator' ), __( 'Labels shown in the calculation results. Leave a label empty to hide that line.', 'wp-mortgage-calculator' ), 'editor-ul' ),
		'defaults'   => array( __( 'Default Values', 'wp-mortgage-calculator' ), __( 'Values pre-filled in the calculator form. Leave empty for a blank form.', 'wp-mortgage-calculator' ), 'edit' ),
	);
}

add_action( 'admin_init', function () {
	register_setting( 'wpmc', 'wpmc_settings', array( 'sanitize_callback' => 'wpmc_sanitize' ) );

	foreach ( wpmc_sections() as $id => $section ) {
		add_settings_section( $id, $section[0], '__return_false', 'wpmc' );
	}
	foreach ( wpmc_fields() as $key => $f ) {
		add_settings_field( $key, $f[0], 'wpmc_field', 'wpmc', $f[1], array( 'key' => $key, 'label_for' => "wpmc-$key" ) );
	}
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( false === strpos( $hook, 'wpmc' ) ) {
		return;
	}
	wp_enqueue_style( 'wpmc-admin', WPMC_URL . 'assets/css/admin.css', array(), wpmc_asset_ver( 'assets/css/admin.css' ) );
	wp_enqueue_script( 'wpmc-admin', WPMC_URL . 'assets/js/admin.js', array(), wpmc_asset_ver( 'assets/js/admin.js' ), true );
	wp_localize_script(
		'wpmc-admin',
		'wpmcAdmin',
		array(
			'sizes'  => wpmc_button_sizes(),
			'copied' => __( 'Copied!', 'wp-mortgage-calculator' ),
		)
	);
} );

/**
 * Choices for select fields.
 *
 * @param string $key Field key.
 * @return array value => label
 */
function wpmc_choices( $key ) {
	if ( 'currency' === $key ) {
		$out = array();
		foreach ( wpmc_currencies() as $code => $c ) {
			$out[ $code ] = "$c[0] ($c[1]) — $code";
		}
		return $out;
	}
	if ( 'btn_size' === $key ) {
		return wp_list_pluck( wpmc_button_sizes(), 0 );
	}
	return array(
		'before' => __( 'Before ($5000)', 'wp-mortgage-calculator' ),
		'after'  => __( 'After (5000$)', 'wp-mortgage-calculator' ),
	);
}

/**
 * Render one settings field.
 *
 * @param array $args Field args.
 */
function wpmc_field( $args ) {
	$key   = $args['key'];
	$f     = wpmc_fields()[ $key ];
	$value = wpmc_options()[ $key ];
	$id    = "wpmc-$key";
	$name  = "wpmc_settings[$key]";

	if ( 'select' === $f[2] ) {
		$currencies = wpmc_currencies();
		echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
		foreach ( wpmc_choices( $key ) as $v => $l ) {
			printf(
				'<option value="%s" data-symbol="%s"%s>%s</option>',
				esc_attr( $v ),
				esc_attr( isset( $currencies[ $v ] ) ? $currencies[ $v ][1] : '' ),
				selected( $value, $v, false ),
				esc_html( $l )
			);
		}
		echo '</select>';
	} elseif ( 'color' === $f[2] ) {
		printf(
			'<label class="wpmc-a-color"><input type="color" id="%s" name="%s" value="%s"><code>%s</code></label>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $value ),
			esc_html( $value )
		);
	} else {
		printf(
			'<input type="%s" id="%s" name="%s" value="%s" class="%s"%s>',
			esc_attr( $f[2] ),
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $value ),
			'number' === $f[2] ? 'small-text' : 'regular-text',
			'number' === $f[2] ? ' step="any" min="0"' : ''
		);
	}
	if ( $f[3] ) {
		echo '<p class="description">' . esc_html( $f[3] ) . '</p>';
	}
}

/**
 * Sanitize settings.
 *
 * @param array $input Raw input.
 * @return array
 */
function wpmc_sanitize( $input ) {
	$input = (array) $input;
	$out   = array();
	foreach ( wpmc_defaults() as $key => $default ) {
		$v = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
		switch ( $key ) {
			case 'currency':
				$out[ $key ] = array_key_exists( $v, wpmc_currencies() ) ? $v : 'USD';
				break;
			case 'position':
				$out[ $key ] = 'after' === $v ? 'after' : 'before';
				break;
			case 'btn_size':
				$out[ $key ] = array_key_exists( $v, wpmc_button_sizes() ) ? $v : 'medium';
				break;
			case 'btn_bg':
			case 'btn_text':
			case 'btn_hover':
			case 'accent':
				$out[ $key ] = sanitize_hex_color( $v ) ? sanitize_hex_color( $v ) : $default;
				break;
			case 'decimals':
				$out[ $key ] = min( absint( $v ), 6 );
				break;
			case 'thousand':
			case 'decimal':
				// Not trimmed: a space is a valid thousand separator.
				$out[ $key ] = mb_substr( str_replace( array( '<', '>', '"', '\\' ), '', wp_check_invalid_utf8( $v ) ), 0, 3 );
				break;
			case 'total_amount':
			case 'down_payment':
			case 'interest_rate':
			case 'mortgage_period':
				$out[ $key ] = is_numeric( $v ) && (float) $v >= 0 ? (string) (float) $v : '';
				break;
			default:
				$out[ $key ] = sanitize_text_field( $v );
		}
	}
	return $out;
}

/**
 * Admin page shell with Settings / Help tabs.
 */
function wpmc_admin_page() {
	// Read-only tab switch, no state change, so no nonce is needed.
	$help = isset( $_GET['page'] ) && 'wpmc-help' === sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tabs = array(
		'wpmc'      => array( __( 'Settings', 'wp-mortgage-calculator' ), 'admin-generic' ),
		'wpmc-help' => array( __( 'Help', 'wp-mortgage-calculator' ), 'editor-help' ),
	);
	?>
	<div class="wrap wpmc-admin">
		<header class="wpmc-a-header">
			<div class="wpmc-a-brand">
				<span class="wpmc-a-logo dashicons dashicons-calculator" aria-hidden="true"></span>
				<div>
					<h1><?php esc_html_e( 'Wp Mortgage Calculator', 'wp-mortgage-calculator' ); ?></h1>
					<p><?php echo esc_html( sprintf( /* translators: %s: version */ __( 'Version %s · by Manpreet Singh', 'wp-mortgage-calculator' ), WPMC_VERSION ) ); ?></p>
				</div>
			</div>
			<nav class="wpmc-a-tabs">
				<?php foreach ( $tabs as $slug => $tab ) : $active = ( 'wpmc-help' === $slug ) === $help; ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>" class="<?php echo $active ? 'is-active' : ''; ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
						<span class="dashicons dashicons-<?php echo esc_attr( $tab[1] ); ?>" aria-hidden="true"></span><?php echo esc_html( $tab[0] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</header>
		<hr class="wp-header-end">
		<?php $help ? wpmc_help() : wpmc_settings_page(); ?>
	</div>
	<?php
}

/**
 * Settings tab: one card per section plus a sticky sidebar.
 */
function wpmc_settings_page() {
	settings_errors();
	?>
	<form method="post" action="options.php" class="wpmc-a-layout">
		<div class="wpmc-a-main">
			<?php settings_fields( 'wpmc' ); ?>
			<?php foreach ( wpmc_sections() as $id => $section ) : ?>
				<section class="wpmc-a-card">
					<div class="wpmc-a-card-head">
						<span class="wpmc-a-icon dashicons dashicons-<?php echo esc_attr( $section[2] ); ?>" aria-hidden="true"></span>
						<div>
							<h2><?php echo esc_html( $section[0] ); ?></h2>
							<p><?php echo esc_html( $section[1] ); ?></p>
						</div>
					</div>
					<table class="form-table" role="presentation"><?php do_settings_fields( 'wpmc', $id ); ?></table>
				</section>
			<?php endforeach; ?>
		</div>

		<aside class="wpmc-a-side">
			<div class="wpmc-a-card wpmc-a-sticky">
				<h2><?php esc_html_e( 'Live preview', 'wp-mortgage-calculator' ); ?></h2>
				<div class="wpmc-a-preview">
					<span><?php esc_html_e( 'Amount format', 'wp-mortgage-calculator' ); ?></span>
					<strong id="wpmc-preview-amount">&nbsp;</strong>
					<span><?php esc_html_e( 'Button', 'wp-mortgage-calculator' ); ?></span>
					<div><button type="button" id="wpmc-preview-btn" class="wpmc-a-preview-btn"><?php esc_html_e( 'Calculate Mortgage', 'wp-mortgage-calculator' ); ?></button></div>
				</div>

				<h2><?php esc_html_e( 'Shortcode', 'wp-mortgage-calculator' ); ?></h2>
				<div class="wpmc-a-code">
					<code>[wp_mortgage_calculator]</code>
					<button type="button" class="wpmc-a-copy" data-copy="[wp_mortgage_calculator]"><?php esc_html_e( 'Copy', 'wp-mortgage-calculator' ); ?></button>
				</div>

				<?php submit_button( __( 'Save Changes', 'wp-mortgage-calculator' ), 'primary large wpmc-a-save', 'submit', false ); ?>
				<a class="wpmc-a-link" href="<?php echo esc_url( admin_url( 'admin.php?page=wpmc-help' ) ); ?>"><?php esc_html_e( 'Need help? Read the documentation →', 'wp-mortgage-calculator' ); ?></a>
			</div>
		</aside>
	</form>
	<?php
}
