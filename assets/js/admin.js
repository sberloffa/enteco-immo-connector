/**
 * Enteco Immo Connector – Admin JavaScript
 * Handles connection test button on settings page.
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var testBtn = document.getElementById('eic-test-connection');
		var resultEl = document.getElementById('eic-test-result');

		if (!testBtn || !resultEl || typeof eicAdmin === 'undefined') {
			return;
		}

		testBtn.addEventListener('click', function () {
			testBtn.disabled = true;
			resultEl.textContent = eicAdmin.i18n.importing;

			fetch(eicAdmin.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'action=eic_test_connection&nonce=' + encodeURIComponent(eicAdmin.nonce),
			})
				.then(function (r) { return r.json(); })
				.then(function (data) {
					testBtn.disabled = false;
					resultEl.textContent = data.success
						? (data.data && data.data.message ? data.data.message : eicAdmin.i18n.importDone)
						: (data.data && data.data.message ? data.data.message : eicAdmin.i18n.importFail);
					resultEl.style.color = data.success ? 'green' : 'red';
				})
				.catch(function () {
					testBtn.disabled = false;
					resultEl.textContent = eicAdmin.i18n.importFail;
					resultEl.style.color = 'red';
				});
		});
	});
}());
