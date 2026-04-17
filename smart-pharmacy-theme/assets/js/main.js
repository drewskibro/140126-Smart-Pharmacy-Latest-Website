/**
 * Smart Pharmacy — shared front-end behaviour.
 *
 * Contents:
 *   - Scroll progress bar
 *   - Mobile menu open/close
 *   - Animated counters (hero rating count, popular treatments patient count, etc.)
 *   - Horizontal carousels ([data-sp-carousel]) — shop bestsellers + anything else
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

	/* Animated counters -------------------------------------------- */
	// Elements: <span class="counter" data-target="10392">0</span>
	// Ticks from 0 to data-target when first scrolled into view. Respects
	// prefers-reduced-motion by snapping straight to the final value.
	var counters = document.querySelectorAll('.counter');
	if (counters.length === 0) {
		return;
	}

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var formatNumber = function (n) {
		return Math.floor(n).toLocaleString('en-GB');
	};

	var animateCounter = function (el) {
		if (el.dataset.spAnimated === '1') {
			return;
		}
		el.dataset.spAnimated = '1';

		var target = parseInt(el.dataset.target || '0', 10);
		if (isNaN(target) || target <= 0) {
			return;
		}

		if (reduceMotion) {
			el.textContent = formatNumber(target);
			el.classList.add('animated');
			return;
		}

		var duration = 1600; // ms
		var start    = performance.now();

		var tick = function (now) {
			var elapsed = Math.min((now - start) / duration, 1);
			// easeOutCubic
			var eased = 1 - Math.pow(1 - elapsed, 3);
			el.textContent = formatNumber(target * eased);
			if (elapsed < 1) {
				requestAnimationFrame(tick);
			} else {
				el.textContent = formatNumber(target);
				el.classList.add('animated');
			}
		};

		requestAnimationFrame(tick);
	};

	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					animateCounter(entry.target);
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.4 });
		counters.forEach(function (c) { io.observe(c); });
	} else {
		// No IO support — run immediately.
		counters.forEach(animateCounter);
	}

	/* Horizontal carousels ---------------------------------------- */
	// Markup contract:
	//   <div data-sp-carousel>
	//     <button data-sp-carousel-prev>...</button>
	//     <div data-sp-carousel-track> ...cards... </div>
	//     <button data-sp-carousel-next>...</button>
	//   </div>
	// Each click scrolls the track by ~90% of its visible width so
	// the user always sees a sliver of the previous frame, keeping
	// continuity. Disabled state toggles when the track hits either
	// end so the arrow buttons reflect scroll position.
	var carousels = document.querySelectorAll('[data-sp-carousel]');
	carousels.forEach(function (root) {
		var track = root.querySelector('[data-sp-carousel-track]');
		var prev  = root.querySelector('[data-sp-carousel-prev]');
		var next  = root.querySelector('[data-sp-carousel-next]');
		if (!track) {
			return;
		}

		var step = function () {
			return Math.max(240, track.clientWidth * 0.9);
		};

		var updateButtons = function () {
			var atStart = track.scrollLeft <= 4;
			var atEnd   = Math.ceil(track.scrollLeft + track.clientWidth) >= track.scrollWidth - 4;
			if (prev) { prev.disabled = atStart; }
			if (next) { next.disabled = atEnd; }
		};

		if (prev) {
			prev.addEventListener('click', function () {
				track.scrollBy({ left: -step(), behavior: 'smooth' });
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				track.scrollBy({ left: step(), behavior: 'smooth' });
			});
		}
		track.addEventListener('scroll', updateButtons, { passive: true });
		window.addEventListener('resize', updateButtons);
		updateButtons();
	});
})();
