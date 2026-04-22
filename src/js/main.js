document.addEventListener('DOMContentLoaded', () => {
	const initPreloader = () =>
		new Promise((resolve) => {
			const dataUrl = window.plumberTheme && window.plumberTheme.initialDataUrl ? window.plumberTheme.initialDataUrl : '';
			const inlineInitialData = window.plumberTheme && window.plumberTheme.initialData ? window.plumberTheme.initialData : null;

			let preloaderRoot = document.getElementById('plumber-preloader');
			if (!preloaderRoot) {
				resolve();
				return;
			}
			const preloaderShownAt = performance.now();

			const removePreloaderShell = () => {
				document.body.classList.remove('plumber-loading');
				const el = document.getElementById('plumber-preloader');
				if (el && el.parentNode) {
					el.parentNode.removeChild(el);
				}
			};

			if (!dataUrl || !window.lottie || !document.body) {
				removePreloaderShell();
				resolve();
				return;
			}

		let animationContainer = preloaderRoot ? preloaderRoot.querySelector('.plumber-preloader__animation') : null;

		if (!animationContainer) {
			preloaderRoot = document.createElement('div');
			preloaderRoot.id = 'plumber-preloader';
			preloaderRoot.className = 'plumber-preloader';
			preloaderRoot.setAttribute('aria-hidden', 'true');
			animationContainer = document.createElement('div');
			animationContainer.className = 'plumber-preloader__animation';
			preloaderRoot.appendChild(animationContainer);
			document.body.appendChild(preloaderRoot);
		}

		document.body.classList.add('plumber-loading');

		let preloaderHidden = false;

		const hidePreloader = () => {
			if (preloaderHidden) {
				return;
			}

			const minVisibleMs = 100;
			const elapsed = performance.now() - preloaderShownAt;
			const hideDelay = Math.max(0, minVisibleMs - elapsed);

			preloaderHidden = true;
			resolve();
			window.setTimeout(() => {
				preloaderRoot.classList.add('is-hidden');
				document.body.classList.remove('plumber-loading');
				window.setTimeout(() => {
					if (preloaderRoot.parentNode) {
						preloaderRoot.parentNode.removeChild(preloaderRoot);
					}
				}, 950);
			}, hideDelay);
		};

		const animationDataPromise = inlineInitialData
			? Promise.resolve(inlineInitialData)
			: fetch(dataUrl).then((response) => {
				if (!response.ok) {
					throw new Error('Preloader data request failed');
				}
				return response.json();
			});

		animationDataPromise
			.then((animationData) => {
				const animation = window.lottie.loadAnimation({
					container: animationContainer,
					renderer: 'svg',
					loop: false,
					autoplay: true,
					animationData,
				});
				preloaderRoot.classList.add('plumber-preloader--lottie-ready');

				animation.addEventListener('complete', hidePreloader);
				animation.addEventListener('data_failed', hidePreloader);
				window.setTimeout(hidePreloader, 7000);
			})
			.catch(() => {
				hidePreloader();
			});
		});

	const preloaderFinished = initPreloader();

	const applyGlobalLazyMedia = () => {
		const eagerContainers = ['.site-header', '.hero-section', '.page-hero-section', '.plumber-preloader'];
		const isInsideEagerContainer = (element) => eagerContainers.some((selector) => element.closest(selector));

		document.querySelectorAll('img').forEach((img) => {
			const currentLoading = img.getAttribute('loading');
			if (
				img.dataset.noLazy === 'true' ||
				img.classList.contains('skip-lazy') ||
				currentLoading === 'eager' ||
				img.getAttribute('fetchpriority') === 'high' ||
				isInsideEagerContainer(img)
			) {
				return;
			}

			if (!currentLoading || currentLoading === 'auto') {
				img.setAttribute('loading', 'lazy');
			}
			if (!img.getAttribute('decoding')) {
				img.setAttribute('decoding', 'async');
			}
			if (!img.getAttribute('fetchpriority')) {
				img.setAttribute('fetchpriority', 'low');
			}
		});

		document.querySelectorAll('iframe').forEach((iframe) => {
			const currentLoading = iframe.getAttribute('loading');
			if (
				iframe.dataset.noLazy === 'true' ||
				currentLoading === 'eager' ||
				iframe.getAttribute('fetchpriority') === 'high' ||
				isInsideEagerContainer(iframe)
			) {
				return;
			}

			if (!currentLoading || currentLoading === 'auto') {
				iframe.setAttribute('loading', 'lazy');
			}
		});
	};

	applyGlobalLazyMedia();

	const phoneFab = document.querySelector('.site-phone-fab');
	if (phoneFab) {
		const phoneFabMobileMq = window.matchMedia('(max-width: 768px)');
		const phoneFabScrollRevealPx = 250;

		const syncPhoneFabScrollVisibility = () => {
			if (!phoneFabMobileMq.matches) {
				phoneFab.classList.add('site-phone-fab--scroll-visible');
				return;
			}
			if (window.scrollY >= phoneFabScrollRevealPx) {
				phoneFab.classList.add('site-phone-fab--scroll-visible');
			} else {
				phoneFab.classList.remove('site-phone-fab--scroll-visible');
			}
		};

		syncPhoneFabScrollVisibility();
		window.addEventListener('scroll', syncPhoneFabScrollVisibility, { passive: true });
		phoneFabMobileMq.addEventListener('change', syncPhoneFabScrollVisibility);
	}

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
	const whyChoosePagination = document.querySelector('.why-choose__pagination');

	if (whyChooseSlider) {
		let autoScrollInterval = null;
		let resumeTimeout = null;
		let whyChooseSlideIndex = 0;
		const whyChooseDots = whyChoosePagination
			? Array.from(whyChoosePagination.querySelectorAll('.why-choose__pagination-dot'))
			: [];

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

		const getWhyChooseSlides = () => Array.from(whyChooseSlider.querySelectorAll('.why-choose-card'));
		const updateWhyChoosePagination = () => {
			if (!whyChooseDots.length) {
				return;
			}
			whyChooseDots.forEach((dot, index) => {
				const isActive = index === whyChooseSlideIndex;
				dot.classList.toggle('is-active', isActive);
				dot.setAttribute('aria-current', isActive ? 'true' : 'false');
			});
		};

		const getWhyChooseGapPx = () => {
			const styles = window.getComputedStyle(whyChooseSlider);
			const raw = styles.columnGap || styles.gap || '14px';
			const parsed = Number.parseFloat(raw);
			return Number.isFinite(parsed) ? parsed : 14;
		};

		const getWhyChooseScrollLeftForIndex = (index) => {
			const slides = getWhyChooseSlides();
			if (!slides.length || index <= 0) {
				return 0;
			}
			const gap = getWhyChooseGapPx();
			let left = 0;
			const maxIndex = Math.min(index, slides.length - 1);
			for (let i = 0; i < maxIndex; i += 1) {
				left += slides[i].offsetWidth + gap;
			}
			const maxScrollLeft = Math.max(0, whyChooseSlider.scrollWidth - whyChooseSlider.clientWidth);
			return Math.min(left, maxScrollLeft);
		};

		const syncWhyChooseIndexFromScroll = () => {
			const slides = getWhyChooseSlides();
			if (!slides.length) {
				whyChooseSlideIndex = 0;
				return;
			}
			const scrollLeft = whyChooseSlider.scrollLeft;
			let best = 0;
			let bestDist = Infinity;
			slides.forEach((slide, i) => {
				const target = getWhyChooseScrollLeftForIndex(i);
				const dist = Math.abs(target - scrollLeft);
				if (dist < bestDist) {
					bestDist = dist;
					best = i;
				}
			});
			whyChooseSlideIndex = best;
			updateWhyChoosePagination();
		};

		const startAutoScroll = () => {
			clearTimers();
			if (!isMobileViewport()) {
				return;
			}

			const slides = getWhyChooseSlides();
			if (!slides.length) {
				return;
			}

			whyChooseSlideIndex = 0;
			whyChooseSlider.scrollTo({ left: 0, behavior: 'auto' });
			updateWhyChoosePagination();

			autoScrollInterval = window.setInterval(() => {
				if (document.hidden) {
					return;
				}

				const slideList = getWhyChooseSlides();
				if (!slideList.length) {
					return;
				}

				const lastIndex = slideList.length - 1;
				if (whyChooseSlideIndex >= lastIndex) {
					whyChooseSlideIndex = 0;
					whyChooseSlider.scrollTo({ left: 0, behavior: 'smooth' });
					updateWhyChoosePagination();
					return;
				}

				whyChooseSlideIndex += 1;
				const targetLeft = getWhyChooseScrollLeftForIndex(whyChooseSlideIndex);
				whyChooseSlider.scrollTo({ left: targetLeft, behavior: 'smooth' });
				updateWhyChoosePagination();
			}, 3800);
		};

		const pauseThenResume = () => {
			clearTimers();
			if (!isMobileViewport()) {
				return;
			}
			syncWhyChooseIndexFromScroll();
			resumeTimeout = window.setTimeout(() => {
				preloaderFinished.then(() => startAutoScroll());
			}, 4500);
		};

		whyChooseSlider.addEventListener('touchstart', clearTimers, { passive: true });
		whyChooseSlider.addEventListener('pointerdown', clearTimers, { passive: true });
		whyChooseSlider.addEventListener('touchend', pauseThenResume, { passive: true });
		whyChooseSlider.addEventListener('pointerup', pauseThenResume, { passive: true });
		whyChooseSlider.addEventListener('pointercancel', pauseThenResume, { passive: true });
		whyChooseSlider.addEventListener('scroll', () => {
			if (!isMobileViewport()) {
				return;
			}
			syncWhyChooseIndexFromScroll();
		}, { passive: true });

		whyChooseDots.forEach((dot, index) => {
			dot.addEventListener('click', () => {
				clearTimers();
				whyChooseSlideIndex = index;
				const targetLeft = getWhyChooseScrollLeftForIndex(index);
				whyChooseSlider.scrollTo({ left: targetLeft, behavior: 'smooth' });
				updateWhyChoosePagination();
				pauseThenResume();
			});
		});

		document.addEventListener('visibilitychange', () => {
			if (document.hidden) {
				clearTimers();
			} else {
				preloaderFinished.then(() => startAutoScroll());
			}
		});

		window.addEventListener('resize', () => {
			if (!isMobileViewport()) {
				clearTimers();
				whyChooseSlider.scrollLeft = 0;
				whyChooseSlideIndex = 0;
				updateWhyChoosePagination();
				return;
			}
			preloaderFinished.then(() => startAutoScroll());
		});

		updateWhyChoosePagination();
		preloaderFinished.then(() => startAutoScroll());
	}

	const ourServicesRoot = document.querySelector('.our-services');
	const servicesPageSlider = document.querySelector('.services-page-slider.swiper');

	const loadSwiper = (() => {
		let loaderPromise = null;

		return () => {
			if (window.Swiper) {
				return Promise.resolve(window.Swiper);
			}
			if (loaderPromise) {
				return loaderPromise;
			}

			const fallbackSwiperUrl = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';
			const configuredSwiperUrl = window.plumberTheme && window.plumberTheme.swiperBundleUrl
				? window.plumberTheme.swiperBundleUrl
				: fallbackSwiperUrl;

			loaderPromise = new Promise((resolve, reject) => {
				const script = document.createElement('script');
				script.src = configuredSwiperUrl;
				script.defer = true;
				script.onload = () => {
					if (window.Swiper) {
						resolve(window.Swiper);
						return;
					}
					reject(new Error('Swiper loaded without global export'));
				};
				script.onerror = () => reject(new Error('Failed to load Swiper'));
				document.head.appendChild(script);
			});

			return loaderPromise;
		};
	})();

	const initOurServices = () => {
		if (!ourServicesRoot || !window.Swiper) {
			return;
		}

		const tabButtons = ourServicesRoot.querySelectorAll('[data-our-services-tab]');
		const panels = ourServicesRoot.querySelectorAll('[data-our-services-panel]');

		let servicesSwiper = null;
		let direction = 1;
		let autoTimer = null;
		let resumeTimer = null;
		let sliderInteractionAbort = null;
		let resumeOurServicesAuto = () => {};

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

		const destroyOurServicesSwiper = () => {
			clearAutoTimers();
			if (sliderInteractionAbort) {
				sliderInteractionAbort.abort();
				sliderInteractionAbort = null;
			}
			resumeOurServicesAuto = () => {};
			if (servicesSwiper) {
				servicesSwiper.destroy(true, true);
				servicesSwiper = null;
			}
		};

		const setupPingPong = (swiperInstance, sliderEl) => {
			const totalSlides = sliderEl.querySelectorAll('.swiper-slide').length;
			if (totalSlides <= 1) {
				return;
			}

			const goNext = () => {
				const lastIndex = swiperInstance.slides.length - 1;
				const activeIndex = swiperInstance.activeIndex;

				if (activeIndex >= lastIndex) {
					direction = -1;
				} else if (activeIndex <= 0) {
					direction = 1;
				}

				swiperInstance.slideTo(activeIndex + direction);
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

			sliderInteractionAbort = new AbortController();
			const signal = sliderInteractionAbort.signal;

			sliderEl.addEventListener('pointerdown', clearAutoTimers, { passive: true, signal });
			sliderEl.addEventListener('touchstart', clearAutoTimers, { passive: true, signal });
			sliderEl.addEventListener('pointerup', pauseThenResume, { passive: true, signal });
			sliderEl.addEventListener('touchend', pauseThenResume, { passive: true, signal });
			sliderEl.addEventListener('pointercancel', pauseThenResume, { passive: true, signal });

			swiperInstance.on('slideChange', () => {
				const lastIndex = swiperInstance.slides.length - 1;
				if (swiperInstance.activeIndex >= lastIndex) {
					direction = -1;
				} else if (swiperInstance.activeIndex <= 0) {
					direction = 1;
				}
			});

			startAuto();
			resumeOurServicesAuto = startAuto;
		};

		const initOurServicesSwiper = (sliderEl) => {
			destroyOurServicesSwiper();

			if (!sliderEl) {
				return;
			}

			const totalSlides = sliderEl.querySelectorAll('.swiper-slide').length;
			if (!totalSlides) {
				return;
			}

			const paginationElement = sliderEl.querySelector('.our-services-pagination');

			servicesSwiper = new window.Swiper(sliderEl, {
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

			direction = 1;
			setupPingPong(servicesSwiper, sliderEl);
		};

		const setActiveTab = (slug) => {
			tabButtons.forEach((button) => {
				const isMatch = button.getAttribute('data-our-services-tab') === slug;
				button.setAttribute('aria-pressed', isMatch ? 'true' : 'false');
				button.classList.toggle('our-services__filter-button--filled', isMatch);
				button.classList.toggle('our-services__filter-button--outline', !isMatch);
			});

			panels.forEach((panel) => {
				const isMatch = panel.getAttribute('data-our-services-panel') === slug;
				panel.classList.toggle('is-active', isMatch);
				if (isMatch) {
					panel.removeAttribute('hidden');
				} else {
					panel.setAttribute('hidden', '');
				}
			});

			const targetPanel = ourServicesRoot.querySelector(`[data-our-services-panel="${slug}"]`);
			const nextSlider = targetPanel ? targetPanel.querySelector('.our-services-slider.swiper') : null;
			initOurServicesSwiper(nextSlider);
		};

		tabButtons.forEach((button) => {
			button.addEventListener('click', (event) => {
				const href = button.getAttribute('href');
				if (href && href.startsWith('#')) {
					event.preventDefault();
				}

				const slug = button.getAttribute('data-our-services-tab');
				if (!slug) {
					return;
				}

				const currentActive = ourServicesRoot.querySelector('.our-services__panel.is-active');
				const currentSlug = currentActive ? currentActive.getAttribute('data-our-services-panel') : '';
				if (slug === currentSlug) {
					return;
				}

				setActiveTab(slug);
			});
		});

		const initialPanel = ourServicesRoot.querySelector('.our-services__panel.is-active');
		const initialSlug = initialPanel ? initialPanel.getAttribute('data-our-services-panel') : 'residential';
		if (initialSlug) {
			setActiveTab(initialSlug);
		}

		document.addEventListener('visibilitychange', () => {
			if (document.hidden) {
				clearAutoTimers();
			} else {
				resumeOurServicesAuto();
			}
		});
	};

	const initServicesPageSlider = () => {
		if (!servicesPageSlider || !window.Swiper) {
			return;
		}

		new window.Swiper(servicesPageSlider, {
			slidesPerView: 'auto',
			spaceBetween: 20,
			grabCursor: true,
			speed: 650,
			loop: false,
			navigation: {
				nextEl: '.services-page-section__arrow--next',
				prevEl: '.services-page-section__arrow--prev',
			},
		});
	};

	if (ourServicesRoot || servicesPageSlider) {
		const sliderRoots = [ourServicesRoot, servicesPageSlider].filter(Boolean);
		let hasLoadedSwiper = false;

		const bootSwiperFeatures = () => {
			if (hasLoadedSwiper) {
				return;
			}
			hasLoadedSwiper = true;

			loadSwiper()
				.then(() => {
					initOurServices();
					initServicesPageSlider();
				})
				.catch(() => {
					// Keep page interactive even if Swiper CDN is temporarily unavailable.
				});
		};

		// Services page arrows must be interactive immediately on page load.
		if (servicesPageSlider) {
			bootSwiperFeatures();
		}

		if (!servicesPageSlider && 'IntersectionObserver' in window) {
			const swiperObserver = new IntersectionObserver(
				(entries, observer) => {
					const shouldInit = entries.some((entry) => entry.isIntersecting);
					if (!shouldInit) {
						return;
					}
					observer.disconnect();
					bootSwiperFeatures();
				},
				{
					rootMargin: '300px 0px',
					threshold: 0.01,
				}
			);

			sliderRoots.forEach((root) => swiperObserver.observe(root));
		} else {
			bootSwiperFeatures();
		}
	}

	const revealSections = ['.hero-section', '.page-hero-section', '.about-section', '.about-page-section', '.contact-page-section', '.services-page-section', '.why-choose', '.our-services', '.faq-section'];
	const instantRevealSections = ['.about-page-section', '.contact-page-section', '.services-page-section'];

	const normalRevealElements = revealSections
		.filter((selector) => !instantRevealSections.includes(selector))
		.map((selector) => document.querySelector(selector))
		.filter(Boolean);

	const instantRevealElements = instantRevealSections
		.map((selector) => document.querySelector(selector))
		.filter(Boolean);

	if (normalRevealElements.length || instantRevealElements.length) {
		if ('IntersectionObserver' in window) {
			const handleRevealEntries = (entries, observer) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			};

			if (normalRevealElements.length) {
				const revealObserver = new IntersectionObserver(handleRevealEntries, { threshold: 0.2 });
				normalRevealElements.forEach((element) => revealObserver.observe(element));
			}

			if (instantRevealElements.length) {
				const instantRevealObserver = new IntersectionObserver(handleRevealEntries, {
					threshold: 0.01,
					rootMargin: '0px 0px -5% 0px',
				});
				instantRevealElements.forEach((element) => instantRevealObserver.observe(element));
			}
		} else {
			normalRevealElements.forEach((element) => element.classList.add('is-visible'));
			instantRevealElements.forEach((element) => element.classList.add('is-visible'));
		}
	}

	const aboutPageNumbers = document.querySelectorAll('.about-page-item__number[data-count-to]');
	if (aboutPageNumbers.length) {
		const prefersReducedMotion =
			window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		const animateCount = (numberEl) => {
			if (!numberEl || numberEl.dataset.countAnimated === 'true') {
				return;
			}

			const rawValue = (numberEl.dataset.countTo || '0').trim();
			const normalizedValue = rawValue.replace(',', '.');
			const finalValue = Number.parseFloat(normalizedValue);
			const decimalPart = normalizedValue.includes('.') ? normalizedValue.split('.')[1] : '';
			const decimals = decimalPart ? decimalPart.length : 0;

			if (!Number.isFinite(finalValue) || finalValue < 0) {
				numberEl.textContent = '0';
				numberEl.dataset.countAnimated = 'true';
				return;
			}

			if (prefersReducedMotion) {
				numberEl.textContent = rawValue || `${finalValue}`;
				numberEl.dataset.countAnimated = 'true';
				return;
			}

			const duration = 1300;
			const startTime = performance.now();

			const step = (timestamp) => {
				const elapsed = timestamp - startTime;
				const progress = Math.min(elapsed / duration, 1);
				const easedProgress = 1 - Math.pow(1 - progress, 3);
				const value = finalValue * easedProgress;
				numberEl.textContent = decimals > 0 ? value.toFixed(decimals) : `${Math.round(value)}`;

				if (progress < 1) {
					window.requestAnimationFrame(step);
				} else {
					numberEl.textContent = rawValue || `${finalValue}`;
					numberEl.dataset.countAnimated = 'true';
				}
			};

			window.requestAnimationFrame(step);
		};

		if ('IntersectionObserver' in window) {
			const numbersObserver = new IntersectionObserver(
				(entries, observer) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							animateCount(entry.target);
							observer.unobserve(entry.target);
						}
					});
				},
				{ threshold: 0.6 }
			);

			aboutPageNumbers.forEach((numberEl) => numbersObserver.observe(numberEl));
		} else {
			aboutPageNumbers.forEach((numberEl) => animateCount(numberEl));
		}
	}

	const contactSection = document.querySelector('.contact-section');
	if (contactSection) {
		const titleEl = contactSection.querySelector('.contact-section__title');
		const itemEls = contactSection.querySelectorAll('.contact-item');
		const formEl = contactSection.querySelector('.contact-section__form');
		const mapEl = contactSection.querySelector('.contact-section__map');
		const contactTargets = [titleEl, ...Array.from(itemEls), formEl, mapEl].filter(Boolean);

		contactTargets.forEach((el) => el.classList.add('contact-reveal'));

		const contactStaggerMs = 85;
		const prefersReducedMotion =
			window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		const runContactReveal = () => {
			if (!contactTargets.length) {
				return;
			}
			if (prefersReducedMotion) {
				contactTargets.forEach((el) => el.classList.add('is-visible'));
				return;
			}
			contactTargets.forEach((el, index) => {
				window.setTimeout(() => {
					el.classList.add('is-visible');
				}, index * contactStaggerMs);
			});
		};

		if ('IntersectionObserver' in window) {
			const contactObserver = new IntersectionObserver(
				(entries, observer) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							runContactReveal();
							observer.disconnect();
						}
					});
				},
				{
					threshold: 0,
					rootMargin: '0px 0px 0px 0px',
				}
			);
			contactObserver.observe(contactSection);
		} else {
			runContactReveal();
		}
	}

	const faqItems = document.querySelectorAll('.faq-item');

	if (faqItems.length) {
		const setFaqItemState = (item, shouldOpen) => {
			const trigger = item.querySelector('.faq-item__trigger');
			const answer = item.querySelector('.faq-item__answer');

			if (!trigger || !answer) {
				return;
			}

			if (shouldOpen) {
				item.classList.add('is-open');
				trigger.setAttribute('aria-expanded', 'true');
				answer.removeAttribute('hidden');
				answer.style.maxHeight = `${answer.scrollHeight + 4}px`;
				answer.setAttribute('aria-hidden', 'false');
			} else {
				item.classList.remove('is-open');
				trigger.setAttribute('aria-expanded', 'false');
				answer.removeAttribute('hidden');
				answer.style.maxHeight = '0px';
				answer.setAttribute('aria-hidden', 'true');
			}
		};

		faqItems.forEach((item) => {
			setFaqItemState(item, item.classList.contains('is-open'));
		});

		faqItems.forEach((item) => {
			const trigger = item.querySelector('.faq-item__trigger');
			const answer = item.querySelector('.faq-item__answer');

			if (!trigger || !answer) {
				return;
			}

			trigger.addEventListener('click', () => {
				const isOpen = item.classList.contains('is-open');

				if (isOpen) {
					setFaqItemState(item, false);
					return;
				}

				faqItems.forEach((currentItem) => {
					setFaqItemState(currentItem, false);
				});

				setFaqItemState(item, true);
			});
		});

		window.addEventListener('resize', () => {
			faqItems.forEach((item) => {
				if (item.classList.contains('is-open')) {
					const answer = item.querySelector('.faq-item__answer');
					if (answer) {
						answer.style.maxHeight = `${answer.scrollHeight + 4}px`;
					}
				}
			});
		});
	}

	const phoneRows = document.querySelectorAll('.contact-form-wrapper .cf-contact__phone-row');
	if (phoneRows.length) {
		phoneRows.forEach((row) => {
			row.querySelectorAll('br').forEach((lineBreak) => lineBreak.remove());
		});
	}

	const countryCodeSelect = document.querySelector('.contact-form-wrapper select[name="country-code"]');
	if (countryCodeSelect) {
		Array.from(countryCodeSelect.options).forEach((option) => {
			const label = (option.textContent || '').trim().toUpperCase();
			if (label.startsWith('US')) {
				option.dataset.country = 'us';
			} else if (label.startsWith('CA')) {
				option.dataset.country = 'ca';
			}
		});
	}

	const customSelectSources = document.querySelectorAll('.contact-form-wrapper select');

	if (customSelectSources.length) {
		const closeAllCustomSelects = () => {
			document.querySelectorAll('.contact-form-wrapper .wpcf7-form-control-wrap.is-select-open').forEach((wrap) => {
				wrap.classList.remove('is-select-open');
				const dropdown = wrap.querySelector('.cf-custom-select__dropdown');
				if (dropdown) {
					dropdown.hidden = true;
				}
			});
		};

		const buildCustomSelect = (select) => {
			if (!(select instanceof HTMLSelectElement) || select.dataset.customized === 'true') {
				return;
			}

			const wrap = select.closest('.wpcf7-form-control-wrap');
			if (!wrap) {
				return;
			}

			select.dataset.customized = 'true';
			wrap.classList.add('has-custom-select');

			const uiRoot = document.createElement('div');
			uiRoot.className = 'cf-custom-select';

			const trigger = document.createElement('button');
			trigger.type = 'button';
			trigger.className = 'cf-custom-select__trigger';
			trigger.setAttribute('aria-haspopup', 'listbox');
			trigger.setAttribute('aria-expanded', 'false');

			const triggerText = document.createElement('span');
			triggerText.className = 'cf-custom-select__trigger-text';
			trigger.appendChild(triggerText);

			const dropdown = document.createElement('ul');
			dropdown.className = 'cf-custom-select__dropdown';
			dropdown.setAttribute('role', 'listbox');
			dropdown.hidden = true;
			const isCountrySelect = select.name === 'country-code';

			const renderSelectLabel = (targetNode, sourceOption, withFlagClassName) => {
				targetNode.textContent = '';
				const optionLabel = sourceOption ? sourceOption.textContent || '' : '';
				const countryCode = sourceOption ? (sourceOption.dataset.country || '').toLowerCase() : '';

				if (isCountrySelect && (countryCode === 'us' || countryCode === 'ca')) {
					const flag = document.createElement('span');
					flag.className = `cf-custom-select__flag cf-custom-select__flag--${countryCode}`;
					targetNode.appendChild(flag);
				}

				const label = document.createElement('span');
				if (withFlagClassName) {
					label.className = withFlagClassName;
				}
				label.textContent = optionLabel;
				targetNode.appendChild(label);
			};

			const setTriggerLabel = () => {
				const selectedOption = select.options[select.selectedIndex];
				renderSelectLabel(triggerText, selectedOption, 'cf-custom-select__trigger-label');
			};

			const setOptionActiveState = () => {
				const currentValue = select.value;
				dropdown.querySelectorAll('.cf-custom-select__option').forEach((optionElement) => {
					const isActive = optionElement.getAttribute('data-value') === currentValue;
					optionElement.classList.toggle('is-active', isActive);
				});
			};

			Array.from(select.options).forEach((option) => {
				const optionItem = document.createElement('li');
				const optionButton = document.createElement('button');
				optionButton.type = 'button';
				optionButton.className = 'cf-custom-select__option';
				optionButton.setAttribute('role', 'option');
				optionButton.setAttribute('data-value', option.value);
				if (isCountrySelect) {
					optionButton.classList.add('cf-custom-select__option--with-flag');
				}
				renderSelectLabel(optionButton, option, 'cf-custom-select__option-label');

				if (option.disabled) {
					optionButton.disabled = true;
				}

				optionButton.addEventListener('click', () => {
					if (optionButton.disabled) {
						return;
					}

					select.value = option.value;
					select.dispatchEvent(new Event('change', { bubbles: true }));
					setTriggerLabel();
					setOptionActiveState();
					wrap.classList.remove('is-select-open');
					trigger.setAttribute('aria-expanded', 'false');
					dropdown.hidden = true;
				});

				optionItem.appendChild(optionButton);
				dropdown.appendChild(optionItem);
			});

			trigger.addEventListener('click', () => {
				const isOpen = wrap.classList.contains('is-select-open');
				closeAllCustomSelects();
				if (!isOpen) {
					wrap.classList.add('is-select-open');
					trigger.setAttribute('aria-expanded', 'true');
					dropdown.hidden = false;
				} else {
					trigger.setAttribute('aria-expanded', 'false');
				}
			});

			trigger.addEventListener('keydown', (event) => {
				if (event.key === 'Escape') {
					closeAllCustomSelects();
					trigger.setAttribute('aria-expanded', 'false');
				}
			});

			select.addEventListener('change', () => {
				setTriggerLabel();
				setOptionActiveState();
			});

			const parentForm = select.closest('form');
			if (parentForm) {
				parentForm.addEventListener('reset', () => {
					window.setTimeout(() => {
						setTriggerLabel();
						setOptionActiveState();
					}, 0);
				});
			}

			uiRoot.appendChild(trigger);
			uiRoot.appendChild(dropdown);
			wrap.appendChild(uiRoot);

			setTriggerLabel();
			setOptionActiveState();
		};

		customSelectSources.forEach((select) => buildCustomSelect(select));

		document.addEventListener('click', (event) => {
			if (!(event.target instanceof Element)) {
				return;
			}
			if (!event.target.closest('.contact-form-wrapper .has-custom-select')) {
				closeAllCustomSelects();
			}
		});
	}

	if (faqItems.length) {
		if ('IntersectionObserver' in window) {
			const faqItemsObserver = new IntersectionObserver(
				(entries, observer) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							entry.target.classList.add('is-visible');
							observer.unobserve(entry.target);
						}
					});
				},
				{
					threshold: 0.18,
				}
			);

			faqItems.forEach((item) => faqItemsObserver.observe(item));
		} else {
			faqItems.forEach((item) => item.classList.add('is-visible'));
		}
	}

});
