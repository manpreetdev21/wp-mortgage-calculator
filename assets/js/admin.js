/* global wpmcAdmin */
(function () {
	'use strict';

	var el = function (id) { return document.getElementById('wpmc-' + id); },
		btn = el('preview-btn');

	// Live preview of the price format and button settings.
	function update() {
		document.querySelectorAll('.wpmc-a-color input').forEach(function (input) {
			input.nextElementSibling.textContent = input.value;
		});
		if (!btn) { return; }

		var currency = el('currency'),
			symbol = currency.options[currency.selectedIndex].dataset.symbol,
			decimals = Math.min(parseInt(el('decimals').value, 10) || 0, 6),
			parts = (1234567.891).toFixed(decimals).split('.'),
			size = wpmcAdmin.sizes[el('btn_size').value];

		var sep = el('thousand').value;
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, function () { return sep; });
		var amount = parts.join(el('decimal').value);
		el('preview-amount').textContent = el('position').value === 'after' ? amount + symbol : symbol + amount;

		btn.style.setProperty('--bg', el('btn_bg').value);
		btn.style.setProperty('--text', el('btn_text').value);
		btn.style.setProperty('--hover', el('btn_hover').value);
		btn.style.setProperty('--pad', size[1]);
		btn.style.setProperty('--font', size[2]);
	}

	document.addEventListener('input', update);
	document.addEventListener('change', update);
	update();

	// Copy buttons.
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-copy]');
		if (!b) { return; }
		var done = function () {
			var label = b.textContent;
			b.textContent = wpmcAdmin.copied;
			b.classList.add('is-copied');
			setTimeout(function () { b.textContent = label; b.classList.remove('is-copied'); }, 1500);
		};
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(b.dataset.copy).then(done);
		} else {
			// Fallback for non-HTTPS admin.
			var ta = document.createElement('textarea');
			ta.value = b.dataset.copy;
			document.body.appendChild(ta);
			ta.select();
			document.execCommand('copy');
			ta.remove();
			done();
		}
	});
})();
