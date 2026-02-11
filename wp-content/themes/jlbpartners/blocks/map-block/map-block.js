/**
 * Map Block JavaScript
 * Handles map region highlighting on card hover/click
 *
 * @package JLBPartners
 */
(function($) {
	'use strict';

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		mapAccordion();
		mapInteraction();
	});

	/**
	 * Initialize map block functionality
	 */
	function initMapBlock() {
		const mapBlocks = document.querySelectorAll('.jsMapBlock');

		if (!mapBlocks.length) {
			return;
		}

		mapBlocks.forEach(function(block) {
			const cards = block.querySelectorAll('.jsMapBlockCard');
			const map = block.querySelector('.jlb-map-block__map');

			if (!cards.length || !map) {
				return;
			}

			// Add event listeners to each card
			cards.forEach(function(card) {
				const mapRegionId = card.getAttribute('data-map-region');

				if (!mapRegionId) {
					return;
				}

				// Find the corresponding SVG region
				const mapRegion = map.querySelector('#' + mapRegionId);

				if (!mapRegion) {
					return;
				}

				// Mouse enter - highlight map region
				card.addEventListener('mouseenter', function() {
					highlightMapRegion(mapRegion, card);
				});

				// Mouse leave - remove highlight
				card.addEventListener('mouseleave', function() {
					removeMapHighlight(mapRegion, card);
				});

				// Click - toggle active state
				card.addEventListener('click', function() {
					toggleActiveCard(card, cards, mapRegion);
				});

				// Keyboard support (Enter and Space)
				card.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						toggleActiveCard(card, cards, mapRegion);
					}
				});
			});

			// Add event listeners to map regions (if they exist and are interactive)
			const mapRegions = map.querySelectorAll('[id^="map-region-"]');
			if (mapRegions.length === 0) {
				// No map regions found - log warning for debugging
				console.warn('Map Block: No map regions found with IDs starting with "map-region-". Please ensure your SVG has elements with IDs: map-region-dfw-houston, map-region-austin, map-region-metro-dc, map-region-boston, map-region-phoenix, map-region-atlanta');
			}
			mapRegions.forEach(function(region) {
				region.addEventListener('mouseenter', function() {
					const regionId = region.getAttribute('id');
					const correspondingCard = block.querySelector('[data-map-region="' + regionId + '"]');
					if (correspondingCard) {
						highlightMapRegion(region, correspondingCard);
					}
				});

				region.addEventListener('mouseleave', function() {
					const regionId = region.getAttribute('id');
					const correspondingCard = block.querySelector('[data-map-region="' + regionId + '"]');
					if (correspondingCard) {
						removeMapHighlight(region, correspondingCard);
					}
				});

				region.addEventListener('click', function() {
					const regionId = region.getAttribute('id');
					const correspondingCard = block.querySelector('[data-map-region="' + regionId + '"]');
					if (correspondingCard) {
						toggleActiveCard(correspondingCard, cards, region);
					}
				});
			});
		});
	}

	/**
	 * Highlight map region and card
	 *
	 * @param {HTMLElement} mapRegion The SVG region element
	 * @param {HTMLElement} card The card element
	 */
	function highlightMapRegion(mapRegion, card) {
		mapRegion.classList.add('jlb-map-block__region--highlighted');
		card.classList.add('jlb-map-block__card--highlighted');
	}

	/**
	 * Remove highlight from map region and card
	 *
	 * @param {HTMLElement} mapRegion The SVG region element
	 * @param {HTMLElement} card The card element
	 */
	function removeMapHighlight(mapRegion, card) {
		// Only remove if not active
		if (!card.classList.contains('jlb-map-block__card--active')) {
			mapRegion.classList.remove('jlb-map-block__region--highlighted');
			card.classList.remove('jlb-map-block__card--highlighted');
		}
	}

	/**
	 * Toggle active state for card and map region
	 *
	 * @param {HTMLElement} clickedCard The card that was clicked
	 * @param {NodeList} allCards All card elements
	 * @param {HTMLElement} mapRegion The corresponding map region
	 */
	function toggleActiveCard(clickedCard, allCards, mapRegion) {
		// Remove active state from all cards and regions
		allCards.forEach(function(card) {
			card.classList.remove('jlb-map-block__card--active');
			const cardRegionId = card.getAttribute('data-map-region');
			if (cardRegionId) {
				const cardRegion = document.querySelector('#' + cardRegionId);
				if (cardRegion) {
					cardRegion.classList.remove('jlb-map-block__region--active');
				}
			}
		});

		// Toggle active state on clicked card
		if (clickedCard.classList.contains('jlb-map-block__card--active')) {
			// If already active, deactivate
			clickedCard.classList.remove('jlb-map-block__card--active');
			if (mapRegion) {
				mapRegion.classList.remove('jlb-map-block__region--active');
			}
		} else {
			// Activate clicked card
			clickedCard.classList.add('jlb-map-block__card--active');
			if (mapRegion) {
				mapRegion.classList.add('jlb-map-block__region--active');
			}
		}
	}

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initMapBlock);
	} else {
		initMapBlock();
	}

	function mapAccordion() {
		// Remove any existing handlers to prevent duplicates
		$('.js-map-accordion .js-location-header').off('click.mapBlock');
		
		// Attach handler scoped to map-block containers only
		$('.js-map-accordion .js-location-header').on('click.mapBlock', function(e) {
			// Stop event propagation to prevent other handlers from firing
			e.stopPropagation();
			
			// Disable accordion for screens above 1199px
			if (window.innerWidth > 1199) {
				return;
			}
			
			var $parent = $(this).parent();
			var $content = $(this).next('.js-location-content');
			
			// If already active, don't do anything
			if ($parent.hasClass('is-active')) {
				return;
			}
			
			// Close all other blocks within map-block container
			$('.js-map-accordion .js-location-block').not($parent).removeClass('is-active');
			$('.js-map-accordion .js-location-content').not($content).stop(true, true).slideUp();
			
			// Open clicked block
			$parent.addClass('is-active');
			$content.stop(true, true).slideDown();
			
			// Get the data-region value from the location block
			const dataRegion = $parent.attr('data-region');
			
			// Get all map cells
			const mapBlocks = document.querySelectorAll('.js-map-block');
			const allCells = [];
			mapBlocks.forEach(function(mapBlock) {
				const mapRegionCells = mapBlock.querySelectorAll('.map-block__cell');
				mapRegionCells.forEach(function(cell) {
					allCells.push(cell);
				});
			});
			
			// Remove is-active from all cells
			allCells.forEach(function(cell) {
				cell.classList.remove('is-active');
			});
			
			// Add is-active to the cell that matches the data-region
			if (dataRegion) {
				allCells.forEach(function(cell) {
					if (cell.id === dataRegion) {
						cell.classList.add('is-active');
					}
				});
			}
		});
	}

	function mapInteraction() {
		const mapBlocks = document.querySelectorAll('.js-map-block');
		const locationBlocks = document.querySelectorAll('.js-location-block');
		
		if (mapBlocks.length === 0) {
			return;
		}
		
		// Collect all cells from all map blocks
		const allCells = [];
		mapBlocks.forEach(function(mapBlock) {
			const mapRegionCells = mapBlock.querySelectorAll('.map-block__cell');
			mapRegionCells.forEach(function(cell) {
				allCells.push(cell);
			});
		});

		// Track the clicked cell to preserve its active state
		let clickedCell = null;

		// Helper function to activate a cell and its corresponding location
		function activateCellAndLocation(cell) {
			const mapCellId = cell.id;
			const isMobile = window.innerWidth <= 1199;
			
			// Remove is-active from all cells
			allCells.forEach(function(otherCell) {
				otherCell.classList.remove('is-active');
			});
			
			// Add is-active to cell
			cell.classList.add('is-active');
			
			// Remove is-active from all location blocks first
			locationBlocks.forEach(function(locationBlock) {
				locationBlock.classList.remove('is-active');
			});

			
			// Close all accordion content on mobile
			if (isMobile) {
				$('.js-location-content').slideUp();
			}
			
			// Check if cell id matches any location block's data-region value
			locationBlocks.forEach(function(locationBlock) {
				const dataRegion = locationBlock.getAttribute('data-region');
				if (dataRegion === mapCellId) {
					locationBlock.classList.add('is-active');
					
					// On mobile, open the accordion for this location block
					if (isMobile) {
						const $locationBlock = $(locationBlock);
						const $content = $locationBlock.find('.js-location-content');
						$content.slideDown();
					}
					
				}
			});
		}

		// Helper function to restore clicked cell state
		function restoreClickedCell() {
			if (clickedCell) {
				activateCellAndLocation(clickedCell);
			} else {
				// No clicked cell, remove all active states
				allCells.forEach(function(cell) {
					cell.classList.remove('is-active');
				});
				locationBlocks.forEach(function(locationBlock) {
					locationBlock.classList.remove('is-active');
				});
			}
		}

		// Add click handlers to all cells
		allCells.forEach(function(cell) {
			// Click handler function
			function handleCellClick() {
				const mapCellId = cell.id;
				const isMobile = window.innerWidth <= 1199;



				const $target = $('[data-region="' + mapCellId + '"]');
				const offset = 100;
				if ($target.length) {
					const targetEl = $target[0];
					if (!isCompletelyInViewport(targetEl)) {
						const scrollFunction = function() {
							$('html, body').animate({
								scrollTop: $target.offset().top - offset
							}, 600);
						};
						
						// Add delay on mobile before scrolling
						if (isMobile) {
							setTimeout(scrollFunction, 300);
						} else {
							scrollFunction();
						}
					}
				}
				function isCompletelyInViewport(element) {
					const rect = element.getBoundingClientRect();
					const windowHeight = window.innerHeight || document.documentElement.clientHeight;
				
					return (
						rect.top >= 0 &&
						rect.bottom <= windowHeight
					);
				}
				
				// If cell is already active, keep it active (do nothing)
				if (cell.classList.contains('is-active')) {
					// Just ensure clickedCell is set to this cell
					clickedCell = cell;
					return;
				}
				
				// Activate the cell
				clickedCell = cell;
				activateCellAndLocation(cell);
			}

			// Add click handler
			cell.addEventListener('click', handleCellClick);

			// Add keyboard support (Enter key)
			cell.addEventListener('keydown', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					handleCellClick();
				}
			});

			// Add hover handlers only to cells that don't have is-active class
			// We'll check this dynamically on each hover event
			// Check for hover capability AND ensure it's not a mobile device
			const hasHoverCapability = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
			const isMobileSize = window.innerWidth <= 1199;
			const hasTouchSupport = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
			const canHover = hasHoverCapability && !isMobileSize && !hasTouchSupport;
			
			if (canHover) {
				cell.addEventListener('mouseenter', function() {
				// Only apply hover effect if this cell is not currently active
				if (!cell.classList.contains('is-active')) {
					activateCellAndLocation(cell);
				}
			});

			cell.addEventListener('mouseleave', function() {
				// Only restore if this cell is not the clicked one
				if (cell !== clickedCell) {
					restoreClickedCell();
				}
			});
			}
			
		});
	}
})(jQuery);
