/* global jQuery, Chart, wpmc */
(function ($) {
	'use strict';

	var s = window.wpmc, t = s.i18n, dialog, charts = [], current;

	function money(v, decimals) {
		var parts = Number(v).toFixed(decimals === undefined ? s.decimals : decimals).split('.');
		// Function form so a separator containing '$' isn't read as a replace pattern.
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, function () { return s.thousand; });
		var out = parts.join(s.decimal);
		return s.position === 'after' ? out + s.currency : s.currency + out;
	}

	function esc(str) {
		return $('<div>').text(str).html();
	}

	// Pure calculation used by the results, the popup and the term comparison.
	function calc(total, down, rate, years) {
		var principal = total - down,
			months = Math.round(years * 12),
			r = rate / 100 / 12,
			payment = r ? principal * r / (1 - Math.pow(1 + r, -months)) : principal / months,
			balance = principal,
			totalInterest = 0,
			rows = [];

		for (var m = 1; m <= months; m++) {
			var interest = balance * r,
				paid = m === months ? balance : payment - interest;
			balance = Math.max(balance - paid, 0);
			totalInterest += interest;
			rows.push([m, paid + interest, paid, interest, balance]);
		}

		return {
			total: total,
			down: down,
			principal: principal,
			payment: payment,
			rate: rate,
			years: years,
			rows: rows,
			totalInterest: totalInterest,
			totalPaid: principal + totalInterest,
			totalWithDown: principal + totalInterest + down
		};
	}

	// Monthly rows summed per year: [year, principal, interest, paid, end balance].
	function yearly(rows) {
		var out = [];
		$.each(rows, function (_, row) {
			var y = Math.ceil(row[0] / 12), last = out[out.length - 1];
			if (!last || last[0] !== y) { last = [y, 0, 0, 0, 0]; out.push(last); }
			last[1] += row[2];
			last[2] += row[3];
			last[3] += row[1];
			last[4] = row[4];
		});
		return out;
	}

	function buildDialog() {
		dialog = $(
			'<dialog class="wpmc-dialog" aria-labelledby="wpmc-d-title">' +
				'<header class="wpmc-d-head">' +
					'<div><span class="wpmc-d-eyebrow" data-t="eyebrow"></span><h3 id="wpmc-d-title" data-t="schedule_title"></h3></div>' +
					'<button type="button" class="wpmc-close"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>' +
				'</header>' +
				'<div class="wpmc-d-body">' +
					'<section class="wpmc-top">' +
						'<div class="wpmc-overview">' +
							'<div class="wpmc-hero"><strong class="wpmc-hero-value"></strong><span data-t="monthly"></span><small class="wpmc-hero-sub"></small></div>' +
							'<div class="wpmc-stats"></div>' +
						'</div>' +
						'<div class="wpmc-card"><h4 data-t="over_time"></h4><div class="wpmc-line"><canvas></canvas></div></div>' +
					'</section>' +
					'<section class="wpmc-card wpmc-summary">' +
						'<ul class="wpmc-rows"></ul>' +
						'<div class="wpmc-breakdown"><div class="wpmc-donut"><canvas></canvas><div class="wpmc-donut-center"><small data-t="total_cost"></small><b></b></div></div></div>' +
					'</section>' +
					'<section class="wpmc-card"><h4 class="wpmc-compare-title"></h4><div class="wpmc-compare"></div></section>' +
					'<section class="wpmc-card">' +
						'<div class="wpmc-table-head"><h4 data-t="schedule"></h4>' +
							'<div class="wpmc-seg"><button type="button" data-view="year" data-t="yearly"></button><button type="button" data-view="month" data-t="monthly_view"></button></div>' +
						'</div>' +
						'<div class="wpmc-table-wrap"><table><thead></thead><tbody></tbody></table></div>' +
					'</section>' +
				'</div>' +
			'</dialog>'
		).appendTo('body');

		dialog.find('.wpmc-close').attr('aria-label', t.close);
		dialog.find('[data-t]').each(function () { $(this).text(t[$(this).data('t')]); });

		// Close on × or backdrop click; Esc is handled natively by <dialog>.
		dialog.on('click', function (e) {
			if (e.target === this || $(e.target).closest('.wpmc-close').length) { this.close(); }
		});
		dialog.on('click', '.wpmc-seg button', function () {
			renderTable($(this).data('view'));
		});
	}

	function renderTable(view) {
		var cols = view === 'year'
				? [t.year, t.principal, t.interest, t.total_paid, t.balance]
				: [t.month, t.payment, t.principal, t.interest, t.balance],
			rows = view === 'year' ? yearly(current.rows) : current.rows;

		dialog.find('.wpmc-seg button').each(function () {
			$(this).toggleClass('is-active', $(this).data('view') === view).attr('aria-pressed', $(this).data('view') === view);
		});
		dialog.find('thead').html('<tr>' + $.map(cols, function (c) { return '<th>' + esc(c) + '</th>'; }).join('') + '</tr>');
		dialog.find('tbody').html($.map(rows, function (r) {
			return '<tr><td>' + r[0] + '</td><td>' + esc(money(r[1])) + '</td><td>' + esc(money(r[2])) +
				'</td><td>' + esc(money(r[3])) + '</td><td>' + esc(money(r[4])) + '</td></tr>';
		}).join(''));
		dialog.find('.wpmc-table-wrap').scrollTop(0);
	}

	function renderCharts(res) {
		if (typeof Chart === 'undefined') { dialog.find('canvas').parent().hide(); return; }

		// Destroy the previous charts here rather than on the dialog's 'close' event,
		// which browsers fire on a later frame (and not at all in hidden tabs).
		$.each(charts, function (_, c) { c.destroy(); });
		charts = [];

		var css = getComputedStyle(dialog[0]),
			color = function (name) { return css.getPropertyValue(name).trim(); },
			cPrincipal = color('--wpmc-primary'),
			cInterest = color('--wpmc-interest'),
			cDown = color('--wpmc-down'),
			cBalance = color('--wpmc-text'),
			tooltipMoney = { callbacks: { label: function (c) { return ' ' + (c.dataset.label || c.label) + ': ' + money(c.raw); } } },
			startYear = new Date().getFullYear(),
			years = yearly(res.rows),
			paidP = 0, paidI = 0,
			line = function (label, data, c, dash) {
				return { label: label, data: data, borderColor: c, backgroundColor: c, borderWidth: 2.5, borderDash: dash || [], pointRadius: 0, pointHoverRadius: 4, tension: 0.35 };
			};

		Chart.defaults.font.family = css.fontFamily;
		Chart.defaults.color = color('--wpmc-muted');

		// Balance falls while cumulative principal and interest paid rise.
		charts.push(new Chart(dialog.find('.wpmc-line canvas'), {
			type: 'line',
			data: {
				labels: [startYear].concat($.map(years, function (y) { return startYear + y[0]; })),
				datasets: [
					line(t.balance, [res.principal].concat($.map(years, function (y) { return y[4]; })), cBalance),
					line(t.principal_paid, [0].concat($.map(years, function (y) { paidP += y[1]; return paidP; })), cPrincipal),
					line(t.interest_paid, [0].concat($.map(years, function (y) { paidI += y[2]; return paidI; })), cInterest, [5, 4])
				]
			},
			options: {
				maintainAspectRatio: false,
				interaction: { mode: 'index', intersect: false },
				plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'line', boxWidth: 18 } }, tooltip: tooltipMoney },
				scales: {
					x: { grid: { display: false }, ticks: { maxTicksLimit: 7 } },
					y: { border: { display: false }, grid: { color: color('--wpmc-border') }, ticks: { maxTicksLimit: 5, callback: function (v) { return money(v, 0); } } }
				}
			}
		}));

		var slices = [[t.principal, res.principal, cPrincipal], [t.interest, res.totalInterest, cInterest]];
		if (res.down > 0) { slices.unshift([t.down_payment, res.down, cDown]); }

		charts.push(new Chart(dialog.find('.wpmc-donut canvas'), {
			type: 'doughnut',
			data: {
				labels: $.map(slices, function (x) { return x[0]; }),
				datasets: [{ data: $.map(slices, function (x) { return x[1]; }), backgroundColor: $.map(slices, function (x) { return x[2]; }), borderWidth: 0, hoverOffset: 6 }]
			},
			options: { cutout: '74%', maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: tooltipMoney } }
		}));
	}

	function openSchedule(res) {
		if (!dialog) { buildDialog(); }
		current = res;

		dialog.find('.wpmc-hero-value').text(money(res.payment));
		dialog.find('.wpmc-hero-sub').text(res.years + ' ' + t.years + ' · ' + res.rate + '%');

		dialog.find('.wpmc-stats').html($.map([
			[money(res.payment), t.principal_interest],
			[money(res.principal), t.loan],
			[money(res.down), t.down_payment],
			[money(res.totalInterest), t.total_interest]
		], function (x) {
			return '<div class="wpmc-stat"><strong>' + esc(x[0]) + '</strong><span>' + esc(x[1]) + '</span></div>';
		}).join(''));

		var css = getComputedStyle(dialog[0]),
			dot = function (v) { return '<i style="background:' + css.getPropertyValue(v).trim() + '"></i>'; },
			rows = [
				['', t.total_of.replace('%s', res.rows.length), res.totalPaid, true],
				[dot('--wpmc-primary'), t.principal, res.principal],
				[dot('--wpmc-interest'), t.interest, res.totalInterest]
			];
		if (res.down > 0) { rows.push([dot('--wpmc-down'), t.down_payment, res.down]); }
		rows.push(['', t.total_cost, res.totalWithDown, true]);

		dialog.find('.wpmc-rows').html($.map(rows, function (r) {
			return '<li' + (r[3] ? ' class="is-total"' : '') + '><span>' + r[0] + esc(r[1]) + '</span><b>' + esc(money(r[2])) + '</b></li>';
		}).join(''));
		dialog.find('.wpmc-donut-center b').text(money(res.totalWithDown, 0));

		// Same loan and rate over 15 vs 30 years, as in the classic comparison.
		dialog.find('.wpmc-compare-title').text(t.compare.replace('%s', res.rate));
		dialog.find('.wpmc-compare').html($.map([15, 30], function (y) {
			var c = calc(res.total, res.down, res.rate, y);
			return '<div class="wpmc-term' + (y === res.years ? ' is-current' : '') + '"><span>' + y + ' ' + esc(t.year_term) + '</span>' +
				'<strong>' + esc(money(c.payment)) + '<small>' + esc(t.per_month) + '</small></strong>' +
				'<em>' + esc(t.total_interest) + ': ' + esc(money(c.totalInterest)) + '</em></div>';
		}).join(''));

		renderTable('year');

		dialog[0].showModal();
		dialog.find('.wpmc-d-body').scrollTop(0);
		renderCharts(res); // After showModal so canvases have a size.
	}

	$(document).on('input', '.wpmc-form input', function () {
		this.setCustomValidity('');
	});

	$(document).on('submit', '.wpmc-form', function (e) {
		e.preventDefault();
		var $form = $(this),
			val = function (name) { return parseFloat($form.find('[name="' + name + '"]').val()); },
			total = val('total_amount'),
			down = val('down_payment');

		$form.find('[name="down_payment"]')[0].setCustomValidity(down >= total ? t.down_error : '');
		if (!this.reportValidity()) { return; }

		var res = calc(total, down, val('interest_rate'), val('mortgage_period')),
			$out = $form.siblings('.wpmc-output'),
			$list = $out.find('.wpmc-results').empty(),
			l = s.labels;

		$.each([
			[l.principal, money(res.principal)],
			[l.years, res.years],
			[l.monthly, money(res.payment)],
			[l.total_interest, money(res.totalPaid)],
			[l.total_down, money(res.totalWithDown)]
		], function (_, item) {
			if (item[0]) {
				$list.append($('<li>').append($('<span>').text(item[0]), $('<strong>').text(item[1])));
			}
		});

		$out.data('wpmc', res).prop('hidden', false);
	});

	$(document).on('click', '.wpmc-schedule-btn', function () {
		openSchedule($(this).closest('.wpmc-output').data('wpmc'));
	});
})(jQuery);
