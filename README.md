# Wp Mortgage Calculator

Mortgage calculator widget and shortcode with a built-in amortization schedule popup.

- **Author:** Manpreet Singh
- **Version:** 1.0.0
- **Requires WordPress:** 5.0+
- **License:** [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.txt)

## Features

- Shortcode: `[wp_mortgage_calculator]`
- Widget: **Wp Mortgage Calculator**
- Built-in amortization popup with Chart.js charts (loan balance over time, cost breakdown), 15- vs 30-year comparison and yearly/monthly schedule — no third-party service
- Settings: currency (52 built in), price format, button colors & size, popup accent color, result labels, default values
- Bundles [Chart.js](https://www.chartjs.org/) 4.5.1 (MIT)
- Full usage docs under **Mortgage Calculator → Help** in wp-admin

## Installation

1. Upload the `wp-mortgage-calculator` folder to `/wp-content/plugins/`.
2. Activate the plugin.
3. Configure it under **Mortgage Calculator → Settings**.

## Folder structure

```
wp-mortgage-calculator/
├── wp-mortgage-calculator.php   # Plugin header, constants, loads the files below
├── uninstall.php                # Deletes settings when the plugin is deleted
├── includes/
│   ├── settings.php             # Defaults, options, currencies, fields, button sizes
│   ├── frontend.php             # Shortcode, calculator markup, front-end assets
│   └── class-wpmc-widget.php    # Widget
├── admin/                       # Loaded only in wp-admin
│   ├── admin.php                # Menu, settings registration, sanitizing, settings page
│   └── help.php                 # Help tab
├── assets/
│   ├── css/                     # wpmc.css (front end), admin.css
│   ├── js/                      # wpmc.js (calculator + popup), admin.js
│   └── vendor/                  # chart.umd.min.js (Chart.js 4.5.1, MIT)
├── tests/calc.test.js           # Calculation checks: node tests/calc.test.js
├── readme.txt                   # WordPress.org readme
└── README.md
```

## Changelog

### 1.0.0
- Initial release.
