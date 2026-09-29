=== Wp Mortgage Calculator ===
Contributors: manpreetdev21
Tags: mortgage, calculator, loan, amortization, mortgage calculator
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mortgage calculator shortcode and widget with a built-in amortization popup, charts, 15- vs 30-year comparison and full payment schedule.

== Description ==

Wp Mortgage Calculator adds a fast, easy-to-use mortgage calculator to any page, post or sidebar.

Visitors enter the total amount, down payment, interest rate and term and instantly see their monthly payment. One click opens a detailed amortization popup, built into the plugin, with no third-party service and no data leaving your site.

= Features =

* Shortcode `[wp_mortgage_calculator]` with attributes for title, labels and pre-filled values
* Classic widget for sidebars
* Amortization popup with:
  * Monthly payment summary and stat cards
  * "Loan balance over time" line chart
  * Cost breakdown doughnut chart (principal, interest, down payment)
  * 15- vs 30-year term comparison
  * Yearly / monthly payment schedule
* 52 built-in currencies, plus your own with the `wpmc_currencies` filter
* Price format settings: sign position, thousand/decimal separators, decimals
* Appearance settings: button colors, hover color, button size and popup accent color
* Customizable result labels and default values
* Built-in Help page with full documentation
* Responsive: the popup becomes a full-screen sheet on phones
* Lightweight: assets load only on pages that show the calculator

= Shortcode examples =

`[wp_mortgage_calculator]`

`[wp_mortgage_calculator title="Home Loan Calculator" total_amount="350000" down_payment="70000" interest_rate="6.5" mortgage_period="30"]`

Available attributes: `title`, `total_amount_label`, `down_payment_label`, `interest_rate_label`, `mortgage_period_label`, `button_label`, `total_amount`, `down_payment`, `interest_rate`, `mortgage_period`.

= Third-party libraries =

This plugin bundles [Chart.js](https://www.chartjs.org/) 4.5.1 (MIT License) in `assets/vendor/chart.umd.min.js`. Source code: https://github.com/chartjs/Chart.js

The plugin does not connect to any external service.

== Installation ==

1. Upload the `wp-mortgage-calculator` folder to `/wp-content/plugins/`, or install it from Plugins → Add New.
2. Activate the plugin through the Plugins screen.
3. Go to **Mortgage Calculator → Settings** to choose your currency, button style and default values.
4. Add `[wp_mortgage_calculator]` to any page or post, or add the "Wp Mortgage Calculator" widget to a sidebar.

== Frequently Asked Questions ==

= How is the monthly payment calculated? =

Loan = Total Amount − Down Payment. The monthly payment uses the standard amortization formula M = P × r / (1 − (1 + r)^−n), where r is the monthly interest rate and n is the number of months. At 0% interest the loan is divided evenly over the months.

= Does the calculator send data to a third-party website? =

No. All calculations, charts and the amortization schedule run in the visitor's browser.

= Can I use it in a theme template? =

Yes: `<?php echo do_shortcode( '[wp_mortgage_calculator]' ); ?>`

= Can I add a currency that is not in the list? =

Yes, with the `wpmc_currencies` filter:

`add_filter( 'wpmc_currencies', function ( $c ) { $c['XOF'] = array( 'West African CFA', 'CFA' ); return $c; } );`

= Can I change the chart colors? =

Yes. The principal color follows the Popup Accent Color setting. Other colors can be overridden with CSS variables in your theme, for example:

`.wpmc-dialog { --wpmc-interest: #f59e0b; --wpmc-down: #10b981; }`

= Can I put more than one calculator on a page? =

Yes, each calculator works independently.

= What happens to my settings if I delete the plugin? =

Deleting the plugin from the Plugins screen removes its settings and widget data.

== Screenshots ==

1. Mortgage calculator form and results.
2. Amortization popup with monthly payment, loan balance chart and cost breakdown.
3. 15- vs 30-year comparison and yearly/monthly payment schedule.
4. Settings page with live preview.
5. Built-in Help page.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
