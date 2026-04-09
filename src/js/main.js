document.addEventListener('DOMContentLoaded', async () => {
	const headerInner = document.querySelector('.header-inner');
	const menuToggle = document.querySelector('.menu-toggle');
	const menuLinks = document.querySelectorAll('.main-navigation a');

	if (headerInner && menuToggle) {
		const setMenuState = (isOpen) => {
			headerInner.classList.toggle('is-menu-open', isOpen);
			document.body.classList.toggle('mobile-menu-open', isOpen);
			menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		};

		menuToggle.addEventListener('click', () => {
			const isOpen = !headerInner.classList.contains('is-menu-open');
			setMenuState(isOpen);
		});

		menuLinks.forEach((link) => {
			link.addEventListener('click', () => setMenuState(false));
		});

		window.addEventListener('resize', () => {
			if (window.innerWidth > 768) {
				setMenuState(false);
			}
		});
	}

	const whyChooseSlider = document.querySelector('.why-choose__grid');

	if (whyChooseSlider) {
		let autoScrollInterval = null;
		let resumeTimeout = null;

		const isMobileViewport = () => window.innerWidth < 768;
		const clearTimers = () => {
			if (autoScrollInterval) {
				window.clearInterval(autoScrollInterval);
				autoScrollInterval = null;
			}
			if (resumeTimeout) {
				window.clearTimeout(resumeTimeout);
				resumeTimeout = null;
			}
		};

		const getSlideStep = () => {
			const firstSlide = whyChooseSlider.querySelector('.why-choose-card');
			if (!firstSlide) {
				return 0;
			}
			return firstSlide.getBoundingClientRect().width + 14;
		};

		const startAutoScroll = () => {
			clearTimers();
			if (!isMobileViewport()) {
				return;
			}

			autoScrollInterval = window.setInterval(() => {
				if (document.hidden) {
					return;
				}

				const step = getSlideStep();
				if (!step) {
					return;
				}

				const maxScrollLeft = whyChooseSlider.scrollWidth - whyChooseSlider.clientWidth - 1;
				const nextScrollLeft = whyChooseSlider.scrollLeft + step;

				if (nextScrollLeft >= maxScrollLeft) {
					whyChooseSlider.scrollTo({ left: 0, behavior: 'smooth' });
					return;
				}

				whyChooseSlider.scrollTo({ left: nextScrollLeft, behavior: 'smooth' });
			}, 3800);
		};

		const pauseThenResume = () => {
			clearTimers();
			if (!isMobileViewport()) {
				return;
			}
			resumeTimeout = window.setTimeout(startAutoScroll, 4500);
		};

		whyChooseSlider.addEventListener('touchstart', clearTimers, { passive: true });
		whyChooseSlider.addEventListener('pointerdown', clearTimers, { passive: true });
		whyChooseSlider.addEventListener('touchend', pauseThenResume, { passive: true });
		whyChooseSlider.addEventListener('pointerup', pauseThenResume, { passive: true });
		whyChooseSlider.addEventListener('pointercancel', pauseThenResume, { passive: true });

		document.addEventListener('visibilitychange', () => {
			if (document.hidden) {
				clearTimers();
			} else {
				startAutoScroll();
			}
		});

		window.addEventListener('resize', () => {
			if (!isMobileViewport()) {
				clearTimers();
				whyChooseSlider.scrollLeft = 0;
				return;
			}
			startAutoScroll();
		});

		startAutoScroll();
	}

	const ourServicesSlider = document.querySelector('.our-services-slider.swiper');

	if (ourServicesSlider && window.Swiper) {
		const totalSlides = ourServicesSlider.querySelectorAll('.swiper-slide').length;
		const paginationElement = ourServicesSlider.querySelector('.our-services-pagination');

		const servicesSwiper = new window.Swiper(ourServicesSlider, {
			slidesPerView: 'auto',
			spaceBetween: 64,
			centeredSlides: true,
			grabCursor: true,
			simulateTouch: true,
			speed: 650,
			loop: false,
			pagination: paginationElement ? {
				el: paginationElement,
				clickable: true,
			} : undefined,
		});

		if (totalSlides > 1) {
			let direction = 1;
			let autoTimer = null;
			let resumeTimer = null;

			const clearAutoTimers = () => {
				if (autoTimer) {
					window.clearInterval(autoTimer);
					autoTimer = null;
				}
				if (resumeTimer) {
					window.clearTimeout(resumeTimer);
					resumeTimer = null;
				}
			};

			const goNext = () => {
				const lastIndex = servicesSwiper.slides.length - 1;
				const activeIndex = servicesSwiper.activeIndex;

				if (activeIndex >= lastIndex) {
					direction = -1;
				} else if (activeIndex <= 0) {
					direction = 1;
				}

				servicesSwiper.slideTo(activeIndex + direction);
			};

			const startAuto = () => {
				clearAutoTimers();
				autoTimer = window.setInterval(() => {
					if (document.hidden) {
						return;
					}
					goNext();
				}, 4000);
			};

			const pauseThenResume = () => {
				clearAutoTimers();
				resumeTimer = window.setTimeout(startAuto, 5000);
			};

			ourServicesSlider.addEventListener('pointerdown', clearAutoTimers, { passive: true });
			ourServicesSlider.addEventListener('touchstart', clearAutoTimers, { passive: true });
			ourServicesSlider.addEventListener('pointerup', pauseThenResume, { passive: true });
			ourServicesSlider.addEventListener('touchend', pauseThenResume, { passive: true });
			ourServicesSlider.addEventListener('pointercancel', pauseThenResume, { passive: true });

			servicesSwiper.on('slideChange', () => {
				const lastIndex = servicesSwiper.slides.length - 1;
				if (servicesSwiper.activeIndex >= lastIndex) {
					direction = -1;
				} else if (servicesSwiper.activeIndex <= 0) {
					direction = 1;
				}
			});

			document.addEventListener('visibilitychange', () => {
				if (document.hidden) {
					clearAutoTimers();
				} else {
					startAuto();
				}
			});

			startAuto();
		}
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
