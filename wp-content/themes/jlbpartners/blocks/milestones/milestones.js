/**
 * Milestones Block JavaScript
 * Handles keyboard navigation and active state management
 *
 * @package JLBPartners
 */
(function() {
	'use strict';

	/**
	 * Initialize milestones functionality
	 */
	function initMilestones() {
		const milestonesContainers = document.querySelectorAll('.jsMilestones');

		if (!milestonesContainers.length) {
			return;
		}

		milestonesContainers.forEach(function(container) {
			const triggers = container.querySelectorAll('.jsMilestoneTrigger');
			
			if (!triggers.length) {
				return;
			}

			triggers.forEach(function(trigger) {
				// Add click handler
				trigger.addEventListener('click', function(e) {
					e.preventDefault();
					activateMilestone(trigger);
				});

				// Keyboard support (Enter and Space keys)
				trigger.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						activateMilestone(trigger);
					}
				});
			});
		});
	}

	/**
	 * Activate a milestone item
	 *
	 * @param {HTMLElement} trigger The milestone trigger button element
	 */
	function activateMilestone(trigger) {
		const item = trigger.closest('.jsMilestoneItem');
		const container = trigger.closest('.jsMilestones');
		
		if (!item || !container) {
			return;
		}

		// Get all items in this container
		const allItems = container.querySelectorAll('.jsMilestoneItem');
		const allTriggers = container.querySelectorAll('.jsMilestoneTrigger');
		const allTitles = container.querySelectorAll('.jsMilestoneTitle');

		// Deactivate all items
		allItems.forEach(function(milestoneItem) {
			milestoneItem.classList.remove('jlb-milestones__item--active');
		});

		allTriggers.forEach(function(milestoneTrigger) {
			milestoneTrigger.setAttribute('aria-expanded', 'false');
		});

		// Hide all titles
		allTitles.forEach(function(title) {
			title.style.display = 'none';
		});

		// Activate the clicked item
		item.classList.add('jlb-milestones__item--active');
		trigger.setAttribute('aria-expanded', 'true');
		
		// Show the title for the active item, or create it if it doesn't exist
		let activeTitle = item.querySelector('.jsMilestoneTitle');
		if (!activeTitle) {
			// Get title from data attribute and create it
			const titleContent = item.getAttribute('data-title');
			if (titleContent) {
				activeTitle = document.createElement('div');
				activeTitle.className = 'jlb-milestones__title jsMilestoneTitle';
				activeTitle.innerHTML = titleContent;
				trigger.appendChild(activeTitle);
			}
		}
		
		if (activeTitle) {
			activeTitle.style.display = 'block';
		}
	}

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initMilestones);
	} else {
		initMilestones();
	}

	// Re-initialize for dynamically loaded content
	if (typeof acf !== 'undefined' && acf.addAction) {
		acf.addAction('render_block_preview/type=milestones', initMilestones);
	}
})();
