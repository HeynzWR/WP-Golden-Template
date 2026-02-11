/**
 * Filter Cards Block JavaScript
 * Handles filtering functionality, keyboard navigation, and hover popups
 *
 * @package JLBPartners
 */
(function() {
	'use strict';

	/**
	 * Initialize filter cards functionality
	 */
	function initFilterCards() {
		const filterContainers = document.querySelectorAll('.jsFilterCards');

		if (!filterContainers.length) {
			return;
		}

		filterContainers.forEach(function(container) {
			const filters = container.querySelectorAll('.jsFilterCardsFilter');
			const cards = container.querySelectorAll('.jsFilterCardsCard');
			const popup = container.querySelector('.jsCardPopup');
			const hoverCards = container.querySelectorAll('.jsCardHover');

			if (!filters.length || !cards.length) {
				return;
			}

			// Initialize filter functionality
			filters.forEach(function(filter) {
				// Add click handler
				filter.addEventListener('click', function(e) {
					e.preventDefault();
					activateFilter(filter, filters, cards);
				});

				// Keyboard support (Enter and Space keys)
				filter.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						activateFilter(filter, filters, cards);
					}
				});
			});

		// Initialize click popup functionality
		if (popup && hoverCards.length) {
			initClickPopup(hoverCards, popup, container);
		}

		// Initialize card tabindex based on visibility
		cards.forEach(function(card) {
			const cardElement = card.querySelector('.jsCardHover');
			if (cardElement) {
				if (card.classList.contains('filter-blocks__col--hidden')) {
					cardElement.setAttribute('tabindex', '-1');
				} else {
					cardElement.setAttribute('tabindex', '0');
				}
			}
		});
		});
	}

	/**
	 * Initialize click popup functionality
	 *
	 * @param {NodeList} cards All card elements
	 * @param {HTMLElement} popup The popup element
	 * @param {HTMLElement} container The filter cards container
	 */
	function initClickPopup(cards, popup, container) {
		const popupTitle = popup.querySelector('.jsPopupTitle');
		const popupSubtitle = popup.querySelector('.jsPopupSubtitle');
		const popupContent = popup.querySelector('.jsPopupContent');
		const popupClose = popup.querySelector('.jsPopupClose');
		const popupOverlay = container.querySelector('.js-popup-overlay');

		if (!popupTitle || !popupSubtitle || !popupContent) {
			return;
		}

		let currentlyOpenCard = null;
		let previousActiveElement = null;
		let focusTrapHandler = null;

		/**
		 * Get all focusable elements inside the popup
		 *
		 * @returns {HTMLElement[]} Array of focusable elements
		 */
		function getFocusableElements() {
			const focusableSelectors = [
				'a[href]',
				'button:not([disabled])',
				'textarea:not([disabled])',
				'input:not([disabled])',
				'select:not([disabled])',
				'[tabindex]:not([tabindex="-1"])'
			].join(', ');

			return Array.from(popup.querySelectorAll(focusableSelectors)).filter(function(el) {
				return el.offsetParent !== null && !el.hasAttribute('disabled');
			});
		}

		/**
		 * Trap focus inside the popup
		 *
		 * @param {KeyboardEvent} e The keyboard event
		 */
		function trapFocus(e) {
			if (!popup.classList.contains('is-visible')) {
				return;
			}

			if (e.key !== 'Tab') {
				return;
			}

			const focusableElements = getFocusableElements();
			
			if (focusableElements.length === 0) {
				return;
			}

			const firstElement = focusableElements[0];
			const lastElement = focusableElements[focusableElements.length - 1];

			// If Shift+Tab on first element, move to last element
			if (e.shiftKey && document.activeElement === firstElement) {
				e.preventDefault();
				lastElement.focus();
				return;
			}

			// If Tab on last element, move to first element
			if (!e.shiftKey && document.activeElement === lastElement) {
				e.preventDefault();
				firstElement.focus();
				return;
			}

			// If focus is outside popup, move to first element
			if (!popup.contains(document.activeElement)) {
				e.preventDefault();
				firstElement.focus();
			}
		}

		/**
		 * Exclude popup from tab order
		 */
		function excludePopupFromTabOrder() {
			// Find all potentially focusable elements (including those with tabindex="-1")
			const allFocusableSelectors = [
				'a[href]',
				'button:not([disabled])',
				'textarea:not([disabled])',
				'input:not([disabled])',
				'select:not([disabled])',
				'[tabindex]'
			].join(', ');

			const allElements = Array.from(popup.querySelectorAll(allFocusableSelectors)).filter(function(el) {
				return el.offsetParent !== null && !el.hasAttribute('disabled');
			});

			allElements.forEach(function(el) {
				const currentTabindex = el.getAttribute('tabindex');
				// If element doesn't have tabindex or has tabindex="0", exclude it
				if (currentTabindex === null || currentTabindex === '0') {
					el.setAttribute('data-tabindex-backup', currentTabindex === null ? '' : '0');
					el.setAttribute('tabindex', '-1');
				}
			});
		}

		/**
		 * Restore popup to tab order
		 */
		function restorePopupToTabOrder() {
			// Find all elements that might have been excluded
			const allFocusableSelectors = [
				'a[href]',
				'button:not([disabled])',
				'textarea:not([disabled])',
				'input:not([disabled])',
				'select:not([disabled])',
				'[tabindex]'
			].join(', ');

			const allElements = Array.from(popup.querySelectorAll(allFocusableSelectors)).filter(function(el) {
				return el.offsetParent !== null && !el.hasAttribute('disabled');
			});

			allElements.forEach(function(el) {
				const backupTabindex = el.getAttribute('data-tabindex-backup');
				if (backupTabindex !== null) {
					if (backupTabindex === '') {
						// Element didn't have tabindex originally, remove it
						el.removeAttribute('tabindex');
					} else {
						// Restore original tabindex
						el.setAttribute('tabindex', backupTabindex);
					}
					el.removeAttribute('data-tabindex-backup');
				}
			});
		}

		/**
		 * Close the popup
		 */
		function closePopup() {
			popup.classList.remove('is-visible');
			popup.setAttribute('aria-hidden', 'true');
			if (popupOverlay) {
				popupOverlay.classList.remove('is-visible');
			}
			document.body.classList.remove('is-overflow-hidden');

			// Exclude popup from tab order when hidden
			excludePopupFromTabOrder();

			// Remove focus trap handler
			if (focusTrapHandler) {
				document.removeEventListener('keydown', focusTrapHandler);
				focusTrapHandler = null;
			}

			// Return focus to the element that opened the popup
			if (previousActiveElement && typeof previousActiveElement.focus === 'function') {
				previousActiveElement.focus();
			}

			currentlyOpenCard = null;
			previousActiveElement = null;
		}

		// Initialize popup as excluded from tab order (hidden by default)
		excludePopupFromTabOrder();

		cards.forEach(function(card) {
			// Show/hide popup on click
			card.addEventListener('click', function(e) {
				e.stopPropagation();

				// If clicking the same card, toggle the popup
				if (currentlyOpenCard === card && popup.classList.contains('is-visible')) {
					closePopup();
					return;
				}

				// Store the card that opened the popup (for focus return)
				// Use the card element itself, or fall back to document.activeElement
				previousActiveElement = card;

				// Get card data
				const title = card.getAttribute('data-card-title');
				const subtitle = card.getAttribute('data-card-subtitle');
				const content = card.getAttribute('data-card-content');

				// Update popup content
				popupTitle.textContent = title || '';
				popupSubtitle.textContent = subtitle || '';
				popupContent.innerHTML = content || '';

				popup.classList.add('is-visible');
				popup.setAttribute('aria-hidden', 'false');
				if (popupOverlay) {
					popupOverlay.classList.add('is-visible');
				}
				document.body.classList.add('is-overflow-hidden');
				currentlyOpenCard = card;

				// Restore popup to tab order when visible
				restorePopupToTabOrder();

				// Set up focus trap
				focusTrapHandler = trapFocus;
				document.addEventListener('keydown', focusTrapHandler);

				// Focus the first focusable element in the popup
				setTimeout(function() {
					const focusableElements = getFocusableElements();
					if (focusableElements.length > 0) {
						focusableElements[0].focus();
					} else if (popupClose) {
						popupClose.focus();
					}
				}, 0);
			});

			// Keyboard support for opening popup (Enter and Space keys)
			card.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					e.stopPropagation();
					card.click();
				}
			});
		});

		// Close popup when clicking the close button
		if (popupClose) {
			popupClose.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				closePopup();
			});

			// Keyboard support for close button (Enter and Space keys)
			popupClose.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					e.stopPropagation();
					closePopup();
				}
			});
		}

		// Close popup when clicking outside
		document.addEventListener('click', function(e) {
			if (popup.classList.contains('is-visible') && !popup.contains(e.target)) {
				closePopup();
			}
		});

		// Close popup when pressing Escape key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && popup.classList.contains('is-visible')) {
				e.preventDefault();
				closePopup();
			}
		});

		// Prevent popup clicks from closing it
		popup.addEventListener('click', function(e) {
			e.stopPropagation();
		});
	}

	/**
	 * Activate a filter and show/hide cards accordingly
	 * Clicking an active filter will deactivate it and show all cards
	 *
	 * @param {HTMLElement} activeFilter The filter button that was clicked
	 * @param {NodeList} filters All filter buttons
	 * @param {NodeList} cards All card elements
	 */
	function activateFilter(activeFilter, filters, cards) {
		const filterValue = activeFilter.getAttribute('data-filter');

		if (!filterValue) {
			return;
		}

		// Check if this filter is already active
		const isCurrentlyActive = activeFilter.classList.contains('filter-blocks__nav-button--active');

		if (isCurrentlyActive) {
			// Deactivate the filter and show all cards
			activeFilter.classList.remove('filter-blocks__nav-button--active', 'is-active');
			activeFilter.setAttribute('aria-pressed', 'false');
			activeFilter.blur();

			// Show all cards and make them focusable
			cards.forEach(function(card) {
				card.classList.remove('filter-blocks__col--hidden');
				const cardElement = card.querySelector('.jsCardHover');
				if (cardElement) {
					cardElement.setAttribute('tabindex', '0');
				}
			});
		} else {
			// Activate this filter and deactivate others
			filters.forEach(function(filter) {
				if (filter === activeFilter) {
					filter.classList.add('filter-blocks__nav-button--active', 'is-active');
					filter.setAttribute('aria-pressed', 'true');
				} else {
					filter.classList.remove('filter-blocks__nav-button--active', 'is-active');
					filter.setAttribute('aria-pressed', 'false');
				}
			});

			// Show/hide cards based on filter and manage tabindex
			cards.forEach(function(card) {
				const cardTag = card.getAttribute('data-tag');
				const cardElement = card.querySelector('.jsCardHover');

				if (cardTag === filterValue) {
					card.classList.remove('filter-blocks__col--hidden');
					if (cardElement) {
						cardElement.setAttribute('tabindex', '0');
					}
				} else {
					card.classList.add('filter-blocks__col--hidden');
					if (cardElement) {
						cardElement.setAttribute('tabindex', '-1');
					}
				}
			});
		}
	}

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initFilterCards);
	} else {
		initFilterCards();
	}
})();
