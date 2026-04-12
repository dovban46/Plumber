document.addEventListener('DOMContentLoaded', () => {
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

	const revealSections = ['.hero-section', '.about-section', '.why-choose', '.our-services', '.faq-section'];
	const revealedElements = revealSections
		.map((selector) => document.querySelector(selector))
		.filter(Boolean);

	if (revealedElements.length) {
		if ('IntersectionObserver' in window) {
			const revealObserver = new IntersectionObserver(
				(entries, observer) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							entry.target.classList.add('is-visible');
							observer.unobserve(entry.target);
						}
					});
				},
				{ threshold: 0.2 }
			);

			revealedElements.forEach((element) => revealObserver.observe(element));
		} else {
			revealedElements.forEach((element) => element.classList.add('is-visible'));
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

	/*
	 * Preloader animation from data.json is disabled.
	 * Keeping this note so it can be restored later if needed.
	 */
});
