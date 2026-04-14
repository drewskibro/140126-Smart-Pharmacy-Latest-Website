/**
 * Smart Pharmacy — shared front-end behaviour.
 *
 * Contents:
 *   - Scroll progress bar
 *   - Mobile menu open/close
 */
(function () {
	'use strict';

	/* Scroll progress bar ------------------------------------------- */
	var progressEl = document.getElementById('scroll-progress');
	if (progressEl) {
		var updateProgress = function () {
			var doc = document.documentElement;
			var scrollable = doc.scrollHeight - window.innerHeight;
			if (scrollable <= 0) {
				progressEl.style.width = '0%';
				return;
			}
			var pct = (window.scrollY / scrollable) * 100;
			progressEl.style.width = pct + '%';
		};
		window.addEventListener('scroll', updateProgress, { passive: true });
		window.addEventListener('resize', updateProgress);
		updateProgress();
	}

	/* Mobile menu --------------------------------------------------- */
	var toggleBtn = document.getElementById('sp-mobile-menu-toggle');
	var closeBtn  = document.getElementById('sp-mobile-menu-close');
	var panel     = document.getElementById('sp-mobile-menu');
	var backdrop  = document.getElementById('sp-mobile-menu-backdrop');

	if (toggleBtn && panel && backdrop) {
		var openMenu = function () {
			panel.classList.remove('translate-x-full');
			panel.setAttribute('aria-hidden', 'false');
			backdrop.classList.remove('hidden');
			toggleBtn.setAttribute('aria-expanded', 'true');
			document.body.style.overflow = 'hidden';
		};

		var closeMenu = function () {
			panel.classList.add('translate-x-full');
			panel.setAttribute('aria-hidden', 'true');
			backdrop.classList.add('hidden');
			toggleBtn.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
		};

		toggleBtn.addEventListener('click', openMenu);
		if (closeBtn) {
			closeBtn.addEventListener('click', closeMenu);
		}
		backdrop.addEventListener('click', closeMenu);

		// Close on Escape.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && panel.getAttribute('aria-hidden') === 'false') {
				closeMenu();
			}
		});

		// Close when clicking a menu link (so tapping a nav item also dismisses the panel).
		var menuLinks = panel.querySelectorAll('a');
		menuLinks.forEach(function (link) {
			link.addEventListener('click', closeMenu);
		});
	}
})();
