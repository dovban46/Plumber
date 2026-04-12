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

	const revealSections = ['.hero-section', '.about-section', '.why-choose', '.our-services', '.faq-section', '.contact-section'];
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

	/*
	 * Preloader animation from data.json is disabled.
	 * Keeping this note so it can be restored later if needed.
	 */
});
