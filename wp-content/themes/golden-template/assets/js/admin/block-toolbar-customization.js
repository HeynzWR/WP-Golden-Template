/**
 * Block Toolbar Customization
 * 
 * Customizes the block toolbar to:
 * - Hide the three-dot menu (block options dropdown)
 * - Add custom Duplicate and Delete buttons to the toolbar
 * - Keep the Edit/Preview toggle
 *
 * @package JLBPartners
 */

(function() {
	'use strict';

	// Wait for WordPress to be ready
	if (typeof wp === 'undefined' || !wp.blocks || !wp.element || !wp.components) {
		return;
	}

	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { createElement, Fragment } = wp.element;
	const { BlockControls } = wp.blockEditor;
	const { ToolbarGroup, ToolbarButton } = wp.components;
	const { dispatch, select } = wp.data;

	/**
	 * Add custom toolbar controls to ACF blocks
	 */
	const withCustomToolbar = createHigherOrderComponent(function(BlockEdit) {
		return function(props) {
			// Only apply to ACF blocks (blocks starting with 'acf/')
			if (!props.name.startsWith('acf/')) {
				return createElement(BlockEdit, props);
			}

			const clientId = props.clientId;

			/**
			 * Duplicate the current block
			 */
			const duplicateBlock = function() {
				const block = select('core/block-editor').getBlock(clientId);
				if (block) {
					const clonedBlock = wp.blocks.cloneBlock(block);
					const index = select('core/block-editor').getBlockIndex(clientId);
					dispatch('core/block-editor').insertBlocks(clonedBlock, index + 1);
				}
			};

			/**
			 * Delete the current block
			 */
			const deleteBlock = function() {
				dispatch('core/block-editor').removeBlocks([clientId]);
			};

			return createElement(
				Fragment,
				null,
				createElement(BlockEdit, props),
				createElement(
					BlockControls,
					null,
					createElement(
						ToolbarGroup,
						null,
						createElement(ToolbarButton, {
							icon: 'admin-page',
							label: 'Duplicate Block',
							onClick: duplicateBlock,
							className: 'golden_template-duplicate-button'
						}),
						createElement(ToolbarButton, {
							icon: 'trash',
							label: 'Delete Block',
							onClick: deleteBlock,
							className: 'golden_template-delete-button'
						})
					)
				)
			);
		};
	}, 'withCustomToolbar');

	// Apply the custom toolbar to all blocks
	addFilter(
		'editor.BlockEdit',
		'golden_template/custom-toolbar',
		withCustomToolbar
	);

	/**
	 * Hide the three-dot menu (block options dropdown) via CSS
	 * This is done by adding a class to the body when the editor loads
	 */
	wp.domReady(function() {
		// Add custom class to body for CSS targeting
		document.body.classList.add('golden_template-custom-toolbar');
	});

})();
