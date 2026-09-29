// Calculation checks for assets/js/wpmc.js. Run: node tests/calc.test.js
'use strict';
var assert = require('assert');
var fs = require('fs');
var path = require('path');

// Load wpmc.js with minimal browser stubs and expose its private helpers.
function load(settings) {
	var $ = function () { return { on: function () { return this; } }; };
	$.each = function (a, f) { a.forEach(function (x, i) { f(i, x); }); };
	var sandbox = { document: {}, window: { wpmc: Object.assign({ decimals: 2, thousand: ',', decimal: '.', currency: '$', position: 'before', i18n: {}, labels: {} }, settings) }, jQuery: $ };
	var src = fs.readFileSync(path.join(__dirname, '../assets/js/wpmc.js'), 'utf8')
		.replace('function buildDialog', 'window.__t = { calc: calc, money: money, yearly: yearly }; function buildDialog');
	new Function('window', 'document', 'jQuery', src)(sandbox.window, sandbox.document, $);
	return sandbox.window.__t;
}

var t = load();
var near = function (a, b, eps, msg) { assert.ok(Math.abs(a - b) <= (eps || 0.005), msg + ': ' + a + ' vs ' + b); };

// 1. Published reference payments (standard amortization tables).
[
	[200000, 0, 6, 30, 1199.10],
	[200000, 0, 6, 15, 1687.71],
	[300000, 60000, 5, 30, 1288.37],
	[300000, 60000, 6.5, 30, 1516.96],
	[500, 0, 8, 1, 43.49],
	[100000, 0, 0, 10, 833.33]
].forEach(function (c) {
	near(Math.round(t.calc(c[0], c[1], c[2], c[3]).payment * 100) / 100, c[4], 0.001, 'payment ' + c.slice(0, 4));
});

// 2. Invariants against an independent formula, over random loans.
for (var i = 0; i < 500; i++) {
	var total = 10000 + Math.random() * 2e6,
		down = Math.random() * total * 0.9,
		rate = Math.random() < 0.1 ? 0 : Math.random() * 20,
		years = 1 + Math.floor(Math.random() * 50),
		r = t.calc(total, down, rate, years),
		P = total - down, n = years * 12, mr = rate / 1200,
		expected = mr ? P * mr * Math.pow(1 + mr, n) / (Math.pow(1 + mr, n) - 1) : P / n,
		sumPrincipal = 0, sumInterest = 0, sumPaid = 0;

	near(r.payment, expected, 1e-6 * expected, 'independent formula');
	assert.strictEqual(r.rows.length, n, 'row count');
	r.rows.forEach(function (row) { sumPrincipal += row[2]; sumInterest += row[3]; sumPaid += row[1]; assert.ok(row[4] >= 0, 'no negative balance'); });
	assert.strictEqual(r.rows[n - 1][4], 0, 'paid off');
	near(sumPrincipal, P, 1e-6 * P, 'principal repaid');
	near(sumInterest, r.totalInterest, 1e-6 * P, 'interest total');
	near(sumPaid, r.totalPaid, 1e-6 * P, 'total paid');
	near(r.totalPaid, expected * n, 1e-6 * P, 'total = payment × months');
	near(r.totalWithDown, r.totalPaid + down, 1e-6, 'with down payment');
	if (!rate) { assert.strictEqual(r.totalInterest, 0, '0% has no interest'); }

	var ys = t.yearly(r.rows);
	assert.strictEqual(ys.length, years, 'yearly rows');
	near(ys.reduce(function (s, y) { return s + y[2]; }, 0), r.totalInterest, 1e-6 * P, 'yearly interest');
	assert.strictEqual(ys[ys.length - 1][4], 0, 'yearly paid off');
}

// 3. Money formatting with different settings.
assert.strictEqual(t.money(1234567.891), '$1,234,567.89');
assert.strictEqual(t.money(1234567.891, 0), '$1,234,568');
assert.strictEqual(t.money(999.995), '$1,000.00');
assert.strictEqual(t.money(0), '$0.00');
assert.strictEqual(load({ thousand: '.', decimal: ',', currency: '€', position: 'after' }).money(1234.5), '1.234,50€');
assert.strictEqual(load({ thousand: ' ', currency: 'kr', position: 'after', decimals: 0 }).money(250000), '250 000kr');
assert.strictEqual(load({ thousand: '$&' }).money(1234), '$1$&234.00', 'special replace chars');
assert.strictEqual(load({ decimals: '3' }).money(1.5), '$1.500', 'decimals arrive as string from wp_localize_script');

console.log('All calculation checks passed.');
