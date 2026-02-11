/**
 * Editor Customization Scripts
 *
 * Enhances the block editor for JLB Partners components.
 *
 * @package JLBPartners
 */

(function() {
	'use strict';

	wp.domReady(function() {
		// Remove core block patterns and categories
		wp.data.dispatch('core/edit-post').setIsEditingTemplate(false);
		
		// Customize block inserter
		customizeBlockInserter();
		
		// Add component preview enhancements
		addComponentPreviews();
		
		// Customize editor sidebar
		customizeEditorSidebar();
		
		// Add keyboard shortcuts info
		addKeyboardShortcuts();
	});

	/**
	 * Customize block inserter to focus on JLB Partners components
	 */
	function customizeBlockInserter() {
		// Add custom CSS classes to inserter
		const inserterObserver = new MutationObserver(function(mutations) {
			const inserter = document.querySelector('.block-editor-inserter__content');
			if (inserter && !inserter.classList.contains('jlbpartners-inserter')) {
				inserter.classList.add('jlbpartners-inserter');
				
				// Add helper text
				addInserterHelperText();
			}
		});

		inserterObserver.observe(document.body, {
			childList: true,
			subtree: true
		});
	}

	/**
	 * Add helper text to block inserter
	 */
	function addInserterHelperText() {
		const inserterHeader = document.querySelector('.block-editor-inserter__panel-header');
		if (!inserterHeader || document.querySelector('.jlbpartners-inserter-help')) {
			return;
		}

		const helpText = document.createElement('div');
		helpText.className = 'jlbpartners-inserter-help';
		helpText.innerHTML = `
			<div class="jlbpartners-inserter-help__content">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
					<path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V5h2v6z" fill="#00a400"/>
				</svg>
				<span>Click any component below to see a preview and add it to your page</span>
			</div>
		`;
		
		inserterHeader.parentNode.insertBefore(helpText, inserterHeader.nextSibling);
	}

	/**
	 * Add enhanced component previews
	 */
	function addComponentPreviews() {
		// Watch for block hover states
		document.addEventListener('mouseover', function(e) {
			const blockItem = e.target.closest('.block-editor-block-types-list__item');
			if (!blockItem) return;

			const blockId = blockItem.getAttribute('data-id');
			if (blockId && blockId.startsWith('acf/')) {
				showEnhancedPreview(blockItem, blockId);
			}
		}, true);
	}

	/**
	 * Show enhanced preview for components
	 */
	function showEnhancedPreview(blockItem, blockId) {
		// Add component badge
		if (!blockItem.querySelector('.jlbpartners-component-badge')) {
			const badge = document.createElement('span');
			badge.className = 'jlbpartners-component-badge';
			badge.textContent = 'Component';
			
			const title = blockItem.querySelector('.block-editor-block-types-list__item-title');
			if (title) {
				title.appendChild(badge);
			}
		}

		// Add preview indicator
		if (!blockItem.querySelector('.jlbpartners-preview-indicator')) {
			const indicator = document.createElement('div');
			indicator.className = 'jlbpartners-preview-indicator';
			indicator.innerHTML = '👁️ Live preview available';
			
			const description = blockItem.querySelector('.block-editor-block-types-list__item-description');
			if (description) {
				description.appendChild(indicator);
			}
		}
	}

	/**
	 * Customize editor sidebar
	 */
	function customizeEditorSidebar() {
		// Add branding indicator
		const sidebar = document.querySelector('.edit-post-sidebar');
		if (sidebar && !document.querySelector('.jlbpartners-branding-indicator')) {
			const brandingIndicator = document.createElement('div');
			brandingIndicator.className = 'jlbpartners-branding-indicator';
			brandingIndicator.innerHTML = `
				<div class="jlbpartners-branding-indicator__content">
					<strong>🎨 Theme Colors & Fonts</strong>
					<p>All components automatically use your branding settings from <a href="/wp-admin/admin.php?page=jlbpartners-settings">JLB Partners → Settings</a></p>
				</div>
			`;
			
			sidebar.prepend(brandingIndicator);
		}
	}

	/**
	 * Add keyboard shortcuts info
	 */
	function addKeyboardShortcuts() {
		// Listen for keyboard shortcut to show help
		document.addEventListener('keydown', function(e) {
			// Cmd/Ctrl + Shift + H
			if ((e.metaKey || e.ctrlKey) && e.shiftKey && e.key === 'H') {
				e.preventDefault();
				showKeyboardShortcuts();
			}
		});
	}

	/**
	 * Show keyboard shortcuts modal
	 */
	function showKeyboardShortcuts() {
		wp.data.dispatch('core/notices').createInfoNotice(
			'Keyboard Shortcuts: Cmd/Ctrl + K = Insert component | Cmd/Ctrl + Z = Undo | Cmd/Ctrl + Shift + Z = Redo',
			{
				isDismissible: true,
				type: 'snackbar'
			}
		);
	}

	/**
	 * Improve block appender text
	 */
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'jlbpartners/custom-appender-text',
		function(settings, name) {
			if (name.startsWith('acf/')) {
				// Add custom example for better previews
				if (!settings.example) {
					settings.example = {
						attributes: {
							mode: 'preview'
						}
					};
				}
			}
			return settings;
		}
	);

	/**
	 * Auto-switch to preview mode for ACF blocks
	 */
	wp.data.subscribe(function() {
		const blocks = wp.data.select('core/block-editor').getBlocks();
		
		blocks.forEach(function(block) {
			if (block.name.startsWith('acf/')) {
				const mode = wp.data.select('core/block-editor').getBlockMode(block.clientId);
				
				// Ensure blocks start in preview mode for better UX
				if (mode === 'edit') {
					wp.data.dispatch('core/block-editor').toggleBlockMode(block.clientId);
				}
			}
		});
	});

})();

