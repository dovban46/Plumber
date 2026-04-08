document.addEventListener('DOMContentLoaded', async () => {
	const headerInner = document.querySelector('.header-inner');
	const menuToggle = document.querySelector('.menu-toggle');

	if (headerInner && menuToggle) {
		menuToggle.addEventListener('click', () => {
			const isOpen = headerInner.classList.toggle('is-menu-open');
			menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
	}

	const dataUrl = window.plumberTheme?.initialDataUrl;

	if (!dataUrl) {
		return;
	}

	// Show preloader only on site entry, not on browser refresh (reload).
	const navigationEntries = performance.getEntriesByType('navigation');
	const navigationType = navigationEntries.length ? navigationEntries[0].type : 'navigate';
	const isReload = navigationType === 'reload';

	if (isReload) {
		return;
	}

	// To enable animation on refresh again, comment out the block above:
	// if (isReload) {
	// 	return;
	// }

	const body = document.body;
	const preloader = document.createElement('div');
	preloader.className = 'plumber-preloader';
	preloader.setAttribute('aria-hidden', 'true');
	preloader.innerHTML = '<div class="plumber-preloader__animation"></div>';

	const animationContainer = preloader.querySelector('.plumber-preloader__animation');
	body.classList.add('plumber-loading');
	body.appendChild(preloader);

	let animationInstance = null;
	let isClosed = false;

	const closePreloader = () => {
		if (isClosed) {
			return;
		}

		isClosed = true;
		preloader.classList.add('is-hidden');
		body.classList.remove('plumber-loading');

		window.setTimeout(() => {
			preloader.remove();
			if (animationInstance) {
				animationInstance.destroy();
			}
		}, 450);
	};

	// Fallback: do not block users on slow network or invalid animation.
	const hardTimeoutId = window.setTimeout(closePreloader, 6000);

	try {
		const response = await fetch(dataUrl, {
			credentials: 'same-origin',
		});

		if (!response.ok) {
			throw new Error(`Failed to load initial data: ${response.status}`);
		}

		const initialData = await response.json();
		window.plumberInitialData = initialData;

		document.dispatchEvent(
			new CustomEvent('plumber:initial-data-loaded', {
				detail: initialData,
			})
		);

		if (window.lottie && animationContainer) {
			animationInstance = window.lottie.loadAnimation({
				container: animationContainer,
				renderer: 'svg',
				loop: false,
				autoplay: true,
				animationData: initialData,
			});

			animationInstance.addEventListener('complete', () => {
				window.clearTimeout(hardTimeoutId);
				closePreloader();
			});
		} else {
			window.clearTimeout(hardTimeoutId);
			closePreloader();
		}
	} catch (error) {
		console.error('Initial data load error:', error);
		window.clearTimeout(hardTimeoutId);
		closePreloader();
	}
});
