/**
 * Project Filtering JavaScript - Optimized
 *
 * @package GoldenTemplate
 */

(function ($) {
    'use strict';

    // Project filtering functionality
    const ProjectFilters = {

        // Temporary filter state (before Apply is clicked)
        tempFilters: {
            states: [],
            types: []
        },

        // Current applied filters (from URL)
        currentFilters: {
            states: [],
            types: []
        },

        // Cache for DOM elements to avoid repeated queries
        cache: {
            $filterToggle: null,
            $filterDropdown: null,
            $appliedFiltersList: null,
            $filterText: null,
            $container: null,
            $loading: null,
            $projectsGrid: null,
            $filterButtonWrapper: null
        },

        // AJAX request handlers for cancellation
        ajaxRequests: {
            filters: null,
            availability: null
        },

        // Debounce timer for filter availability updates
        availabilityDebounce: null,

        // Cache for filter availability results
        availabilityCache: new Map(),

        // Scroll detection for projects grid position
        scrollDetection: {
            rafId: null,
            isActive: false
        },

        // Initialize the filtering system
        init: function () {
            this.cacheElements();
            this.bindEvents();
            this.initFilterState();
            this.initScrollDetection();
        },

        // Cache frequently used DOM elements
        cacheElements: function () {
            this.cache.$filterToggle = $('.js-filter-toggle');
            this.cache.$filterDropdown = $('.js-filter-dropdown');
            this.cache.$appliedFiltersList = $('.js-applied-filters-list');
            this.cache.$filterText = $('.projects-listing__filter-text');
            this.cache.$container = $('.projects-listing-container');
            this.cache.$loading = $('.loading-indicator');
            this.cache.$projectsGrid = $('.js-projects-grid');
            this.cache.$filterButtonWrapper = $('.js-filter-button-wrapper');
        },

        // Bind event handlers (using event delegation for better performance)
        bindEvents: function () {
            const $doc = $(document);

            // Filter toggle button
            $doc.on('click', '.js-filter-toggle', this.handleFilterToggle.bind(this));

            // Dot/button clicks for filters - store temporarily, don't apply
            $doc.on('click', '.js-filter-state, .js-filter-type', this.handleFilterDotClick.bind(this));

            // Apply button - apply the filters
            $doc.on('click', '.js-filter-apply', this.handleApplyFilters.bind(this));

            // Close button - close dropdown and reset to current filters
            $doc.on('click', '.js-filter-close', this.handleCloseFilters.bind(this));

            // Remove applied filter tag (in dropdown)
            $doc.on('click', '.js-remove-applied-filter', this.handleRemoveAppliedFilter.bind(this));

            // Remove filter tag (in main display)
            $doc.on('click', '.js-remove-filter-tag', this.handleRemoveFilterTag.bind(this));

            // Close dropdown when clicking outside
            $doc.on('click', this.handleOutsideClick.bind(this));

            // Pagination clicks (for AJAX pagination)
            $doc.on('click', '.projects-pagination a', this.handlePaginationClick.bind(this));

            // Handle browser back/forward buttons
            $(window).on('popstate', this.handlePopState.bind(this));

            // Handle scroll for projects grid position detection
            $(window).on('scroll', this.handleScroll.bind(this));
            $(window).on('resize', this.handleScroll.bind(this));
        },

        // Handle outside click
        handleOutsideClick: function (e) {
            if (!$(e.target).closest('.projects-listing__filter-button-wrapper, .projects-listing__filter-dropdown').length) {
                this.closeFilterDropdown();
            }
        },

        // Initialize filter state from URL
        initFilterState: function () {
            const urlParams = this.getURLParams();
            const states = urlParams.states || [];
            const types = urlParams.types || [];

            // Store current filters
            this.currentFilters.states = states;
            this.currentFilters.types = types;
            this.tempFilters.states = [...states];
            this.tempFilters.types = [...types];

            // Update active state based on URL
            this.updateButtonsFromFilters(states, types);
            
            // Update applied filters display
            this.updateAppliedFilters();
            
            // Update available filter options on initial load
            this.updateAvailableFilterOptions();
            
            // Check and update resize-font class on filter tags
            this.updateFilterTagsResize();
        },

        // Get URL parameters (helper function)
        getURLParams: function () {
            const url = new URL(window.location.href);
            return {
                states: url.searchParams.get('states') ? url.searchParams.get('states').split(',').filter(Boolean) : [],
                types: url.searchParams.get('types') ? url.searchParams.get('types').split(',').filter(Boolean) : [],
                paged: url.searchParams.get('paged') || 1
            };
        },

        // Handle filter toggle
        handleFilterToggle: function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isExpanded = this.cache.$filterToggle.attr('aria-expanded') === 'true';

            if (isExpanded) {
                this.closeFilterDropdown();
            } else {
                this.openFilterDropdown();
            }
        },

        // Open filter dropdown
        openFilterDropdown: function () {
            this.cache.$filterToggle.attr('aria-expanded', 'true');
            this.cache.$filterDropdown.attr('aria-hidden', 'false').addClass('is-open');
            
            // Reset temp filters to current filters when opening
            this.tempFilters.states = [...this.currentFilters.states];
            this.tempFilters.types = [...this.currentFilters.types];
            
            // Update buttons to show current state
            this.updateButtonsFromFilters(this.currentFilters.states, this.currentFilters.types);
            
            // Update applied filters display
            this.updateAppliedFilters();
            
            // Update available filter options
            this.updateAvailableFilterOptions();

            // Trigger dot falling animation
            this.triggerDotAnimation();
        },

        // Close filter dropdown
        closeFilterDropdown: function () {
            this.cache.$filterToggle.attr('aria-expanded', 'false');
            this.cache.$filterDropdown.attr('aria-hidden', 'true').removeClass('is-open');
        },

        // Handle filter dot/button click - update temporary selection only
        handleFilterDotClick: function (e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            
            // Don't do anything if disabled
            if ($button.hasClass('is-disabled')) {
                return;
            }

            const value = $button.data('value');
            const filterType = $button.data('filter-type');
            const isState = filterType === 'state';
            const selector = isState ? '.js-filter-state' : '.js-filter-type';
            const $anyButton = $(selector + '[data-value="any"]');

            // Handle "Any" option
            if (value === 'any') {
                // If "Any" is clicked, deselect all others in this group
                $(selector).removeClass('is-active');
                $button.addClass('is-active');
            } else {
                // If a specific option is clicked, deselect "Any"
                $anyButton.removeClass('is-active');
                
                // Toggle the clicked option
                $button.toggleClass('is-active');
                
                // If no specific options are selected, select "Any"
                if ($(selector + ':not([data-value="any"]).is-active').length === 0) {
                    $anyButton.addClass('is-active');
                }
            }

            // Update temporary filters (don't apply yet)
            this.updateTempFilters();
            this.updateAppliedFilters();
            
            // Update available filters based on current selections (debounced)
            this.debouncedUpdateAvailability();
        },

        // Update temporary filters from current button selections
        updateTempFilters: function () {
            this.tempFilters.states = this.getSelectedFilters('.js-filter-state');
            this.tempFilters.types = this.getSelectedFilters('.js-filter-type');
        },

        // Get selected filters for a given selector (optimized)
        getSelectedFilters: function (selector) {
            const $anyActive = $(selector + '[data-value="any"].is-active');
            
            // If "Any" is selected, return empty array (means all)
            if ($anyActive.length > 0) {
                return [];
            }
            
            // Otherwise, return all selected specific values
            const filters = [];
            $(selector + '.is-active').each(function() {
                const value = $(this).data('value');
                if (value && value !== 'any') {
                    filters.push(value);
                }
            });
            return filters;
        },

        // Handle Apply button click
        handleApplyFilters: function (e) {
            e.preventDefault();
            
            // Update temp filters from current selections
            this.updateTempFilters();
            
            // Apply the filters
            this.currentFilters.states = [...this.tempFilters.states];
            this.currentFilters.types = [...this.tempFilters.types];
            
            // Load projects with new filters
            this.loadProjects(this.currentFilters.states, this.currentFilters.types, 1);
            
            // Close dropdown
            this.closeFilterDropdown();
        },

        // Handle Close button click
        handleCloseFilters: function (e) {
            e.preventDefault();
            
            // Reset temp filters to current filters
            this.tempFilters.states = [...this.currentFilters.states];
            this.tempFilters.types = [...this.currentFilters.types];
            
            // Reset buttons to current state
            this.updateButtonsFromFilters(this.currentFilters.states, this.currentFilters.types);
            
            // Update applied filters display
            this.updateAppliedFilters();
            
            // Close dropdown
            this.closeFilterDropdown();
        },

        // Handle remove applied filter tag (in dropdown)
        handleRemoveAppliedFilter: function (e) {
            e.preventDefault();
            e.stopPropagation();
            const $tag = $(e.currentTarget).closest('.projects-listing__applied-filter-tag');
            const filterType = $tag.data('filter-type');
            const value = $tag.data('value');
            
            // Remove from temp filters
            if (filterType === 'state') {
                this.tempFilters.states = this.tempFilters.states.filter(s => s !== value);
            } else {
                this.tempFilters.types = this.tempFilters.types.filter(t => t !== value);
            }
            
            // Update button states
            $('.js-filter-' + filterType + '[data-value="' + value + '"]').removeClass('is-active');
            
            // If no specific filters selected, select "Any"
            const selector = filterType === 'state' ? '.js-filter-state' : '.js-filter-type';
            if ($(selector + ':not([data-value="any"]).is-active').length === 0) {
                $(selector + '[data-value="any"]').addClass('is-active');
            }
            
            // Update applied filters display
            this.updateAppliedFilters();
            
            // Update available filter options (debounced)
            this.debouncedUpdateAvailability();
        },

        // Handle remove filter tag (in main display - applies immediately)
        handleRemoveFilterTag: function (e) {
            e.preventDefault();
            e.stopPropagation();
            const $tag = $(e.currentTarget).closest('.projects-listing__filter-tag');
            const filterType = $tag.data('filter-type');
            const value = $tag.data('value');
            
            // Remove from current filters
            if (filterType === 'state') {
                this.currentFilters.states = this.currentFilters.states.filter(s => s !== value);
            } else {
                this.currentFilters.types = this.currentFilters.types.filter(t => t !== value);
            }
            
            // Update temp filters to match
            this.tempFilters.states = [...this.currentFilters.states];
            this.tempFilters.types = [...this.currentFilters.types];
            
            // Update button states
            $('.js-filter-' + filterType + '[data-value="' + value + '"]').removeClass('is-active');
            
            // If no specific filters selected, select "Any"
            const selector = filterType === 'state' ? '.js-filter-state' : '.js-filter-type';
            if ($(selector + ':not([data-value="any"]).is-active').length === 0) {
                $(selector + '[data-value="any"]').addClass('is-active');
            }
            
            this.loadProjects(this.currentFilters.states, this.currentFilters.types, 1, true, null, false);
        },

        // Update applied filters display (optimized with fragment)
        updateAppliedFilters: function () {
            const fragment = document.createDocumentFragment();
            
            // Helper function to create filter tag
            const createTag = (type, value, label) => {
                const tag = document.createElement('div');
                tag.className = 'projects-listing__applied-filter-tag';
                tag.setAttribute('data-filter-type', type);
                tag.setAttribute('data-value', value);
                
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'projects-listing__applied-filter-remove js-remove-applied-filter';
                button.setAttribute('aria-label', 'Remove ' + label);
                
                const span = document.createElement('span');
                span.textContent = label;
                
                tag.appendChild(button);
                tag.appendChild(span);
                return tag;
            };
            
            // Add state filters
            this.tempFilters.states.forEach(state => {
                const $button = $('.js-filter-state[data-value="' + state + '"]');
                const label = $button.data('label') || $button.find('.projects-listing__filter-option-label').text();
                fragment.appendChild(createTag('state', state, label));
            });
            
            // Add type filters
            this.tempFilters.types.forEach(type => {
                const $button = $('.js-filter-type[data-value="' + type + '"]');
                const label = $button.data('label') || $button.find('.projects-listing__filter-option-label').text();
                fragment.appendChild(createTag('type', type, label));
            });
            
            // Update DOM once
            this.cache.$appliedFiltersList.empty().append(fragment);
        },

        // Debounced update for filter availability
        debouncedUpdateAvailability: function () {
            clearTimeout(this.availabilityDebounce);
            this.availabilityDebounce = setTimeout(() => {
                this.updateAvailableFilterOptions();
            }, 250); // Wait 250ms after last interaction
        },

        // Update available filter options based on current selections
        updateAvailableFilterOptions: function () {
            const states = this.tempFilters.states.join(',');
            const types = this.tempFilters.types.join(',');
            
            // Check cache first
            const cacheKey = `${states}|${types}`;
            if (this.availabilityCache.has(cacheKey)) {
                this.applyFilterAvailability(this.availabilityCache.get(cacheKey));
                return;
            }
            
            // Cancel previous request if still pending
            if (this.ajaxRequests.availability && this.ajaxRequests.availability.abort) {
                this.ajaxRequests.availability.abort();
            }
            
            // AJAX request to get available filters
            this.ajaxRequests.availability = $.ajax({
                url: golden_templateData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_available_filters',
                    states: states,
                    types: types,
                    nonce: golden_templateData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        const data = {
                            availableStates: response.data.available_states || [],
                            availableTypes: response.data.available_types || []
                        };
                        
                        // Cache the result
                        this.availabilityCache.set(cacheKey, data);
                        
                        // Apply the availability
                        this.applyFilterAvailability(data);
                    }
                },
                error: (xhr) => {
                    // Only log if not aborted
                    if (xhr.statusText !== 'abort') {
                        console.error('Error fetching filter availability');
                        // On error, enable all options
                        $('.js-filter-state, .js-filter-type').removeClass('is-disabled');
                    }
                }
            });
        },

        // Apply filter availability to buttons (optimized)
        applyFilterAvailability: function (data) {
            const { availableStates, availableTypes } = data;
            
            // Update state options
            $('.js-filter-state').each(function() {
                const $option = $(this);
                const value = $option.data('value');
                
                if (value === 'any') {
                    $option.removeClass('is-disabled');
                } else {
                    const isAvailable = availableStates.includes(value);
                    if (isAvailable) {
                        $option.removeClass('is-disabled');
                    } else if (!$option.hasClass('is-active')) {
                        // Only disable if not currently selected
                        $option.addClass('is-disabled');
                    }
                }
            });
            
            // Update type options
            $('.js-filter-type').each(function() {
                const $option = $(this);
                const value = $option.data('value');
                
                if (value === 'any') {
                    $option.removeClass('is-disabled');
                } else {
                    const isAvailable = availableTypes.includes(value);
                    if (isAvailable) {
                        $option.removeClass('is-disabled');
                    } else if (!$option.hasClass('is-active')) {
                        // Only disable if not currently selected
                        $option.addClass('is-disabled');
                    }
                }
            });
        },

        // Handle pagination clicks
        handlePaginationClick: function (e) {
            e.preventDefault();

            const href = $(e.currentTarget).attr('href');
            const url = new URL(href);
            
            // Extract page number from either ?paged=2 or /page/2/ format
            let page = url.searchParams.get('paged');
            if (!page) {
                const pageMatch = href.match(/\/page\/(\d+)\/?/);
                page = pageMatch ? pageMatch[1] : 1;
            }
            
            const states = url.searchParams.get('states') ? url.searchParams.get('states').split(',').filter(Boolean) : [];
            const types = url.searchParams.get('types') ? url.searchParams.get('types').split(',').filter(Boolean) : [];

            // Load projects for the requested page
            this.loadProjects(states, types, page);
        },

        // Handle browser back/forward navigation
        handlePopState: function (e) {
            const state = e.originalEvent.state;
            if (state && state.states !== undefined && state.types !== undefined && state.page !== undefined) {
                // Update filters
                this.currentFilters.states = state.states;
                this.currentFilters.types = state.types;
                this.tempFilters.states = [...state.states];
                this.tempFilters.types = [...state.types];
                
                // Update buttons
                this.updateButtonsFromFilters(state.states, state.types);

                // Load projects without updating URL (to avoid infinite loop)
                this.loadProjects(state.states, state.types, state.page, false);
            }
        },

        // Update buttons from filter arrays (optimized)
        updateButtonsFromFilters: function (states, types) {
            // Reset all
            $('.js-filter-state, .js-filter-type').removeClass('is-active');
            
            // Set states
            if (states.length > 0) {
                states.forEach(state => {
                    $('.js-filter-state[data-value="' + state + '"]').addClass('is-active');
                });
            } else {
                $('.js-filter-state[data-value="any"]').addClass('is-active');
            }

            // Set types
            if (types.length > 0) {
                types.forEach(type => {
                    $('.js-filter-type[data-value="' + type + '"]').addClass('is-active');
                });
            } else {
                $('.js-filter-type[data-value="any"]').addClass('is-active');
            }
        },

        // Load projects via AJAX
        loadProjects: function (states, types, page, updateHistory = true, callback = null, shouldScroll = true) {
            // Cancel previous request if still pending
            if (this.ajaxRequests.filters && this.ajaxRequests.filters.abort) {
                this.ajaxRequests.filters.abort();
            }

            // Show loading indicator
            this.cache.$container.addClass('loading');
            if (this.cache.$loading.length) {
                this.cache.$loading.show();
            }

            // AJAX request
            this.ajaxRequests.filters = $.ajax({
                url: golden_templateData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'filter_projects',
                    states: states.join(','),
                    types: types.join(','),
                    page: page,
                    nonce: golden_templateData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.handleProjectsLoaded(response.data, states, types, page, updateHistory, callback, shouldScroll);
                    } else {
                        this.handleLoadError('AJAX request failed');
                    }
                },
                error: (xhr) => {
                    if (xhr.statusText !== 'abort') {
                        this.handleLoadError('AJAX error occurred');
                    }
                }
            });
        },

        // Handle successful project load
        handleProjectsLoaded: function (data, states, types, page, updateHistory, callback, shouldScroll = true) {
            const $filterButton = this.cache.$container.find('.projects-listing__filter-button-wrapper');
            const filterButtonExists = $filterButton.length > 0;
            
            // Remove old content (projects, pagination, and no-results messages)
            this.cache.$container.find('.projects-grid, .projects-pagination, .no-projects-found, .ajax-error').remove();
            
            // Insert new projects HTML at the beginning of the container (before filter button)
            if (filterButtonExists) {
                // Insert before the filter button
                $filterButton.before(data.posts_html);
                
                // Insert pagination before filter button (if exists)
                if (data.pagination_html) {
                    $filterButton.before('<nav class="projects-pagination" aria-label="Projects pagination" data-aos="fade-up"   >' + data.pagination_html + '</nav>');
                }
            } else {
                // No filter button found, append normally
                this.cache.$container.append(data.posts_html);
                if (data.pagination_html) {
                    this.cache.$container.append('<nav class="projects-pagination" aria-label="Projects pagination" data-aos="fade-up"   >' + data.pagination_html + '</nav>');
                }
            }

            // Update selected filters display (show/hide entire section based on filters)
            const $filtersSection = $('.projects-listing__filters');
            
            if (data.selected_filters_html) {
                // If filters exist, ensure the section exists and update content
                if ($filtersSection.length === 0) {
                    // Create the filters section if it doesn't exist
                    $('.projects-listing__header').after(
                        '<div class="projects-listing__filters">' +
                        '<div class="projects-listing__filter-tags-wrapper">' +
                        data.selected_filters_html +
                        '</div>' +
                        '</div>'
                    );
                } else {
                    // Update existing section
                    $filtersSection.find('.projects-listing__filter-tags-wrapper').html(data.selected_filters_html);
                }
                
                // Add resize-font class if 3 or more filter tags
                this.updateFilterTagsResize();
            } else {
                // No filters, remove the entire section
                $filtersSection.remove();
            }

            // Hide loading indicator
            this.cache.$container.removeClass('loading');
            if (this.cache.$loading.length) {
                this.cache.$loading.hide();
            }
            
            // Smooth scroll to top of projects grid (not page top) - only if shouldScroll is true
            if (shouldScroll) {
                const $projectsGrid = this.cache.$container.find('.projects-grid, .no-projects-found').first();
                if ($projectsGrid.length && page === 1) {
                    // Only scroll on filter change (page 1), not on pagination
                    const scrollTop = $projectsGrid.offset().top - 100; // 100px offset for breathing room
                    $('html, body').animate({ scrollTop: scrollTop }, 400);
                }
            }

            // Update URL and browser history
            if (updateHistory) {
                this.updateURL(states, types, page);
            }

            // Re-cache projects grid element (may have changed after AJAX load)
            this.cache.$projectsGrid = this.cache.$container.find('.js-projects-grid');
            
            // Re-initialize scroll detection with new grid element
            this.initScrollDetection();

            // Refresh AOS animations for newly loaded content
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }

            // Trigger custom event for other scripts
            $(document).trigger('projects_loaded', [data]);

            // Execute callback if provided
            if (callback && typeof callback === 'function') {
                callback();
            }
        },

        // Handle load error
        handleLoadError: function (message) {
            console.error(message);
            this.cache.$container.removeClass('loading');
            if (this.cache.$loading.length) {
                this.cache.$loading.hide();
            }

            // Show error message
            this.cache.$container.find('.projects-grid, .no-projects-found').remove();
            this.cache.$container.append('<div class="ajax-error"><p>Sorry, there was an error loading the projects. Please try again.</p></div>');
        },

        // Update URL and browser history
        updateURL: function (states = [], types = [], page = 1) {
            page = Math.max(1, parseInt(page)) || 1;
            const url = new URL(document.location.href);

            // Update URL parameters
            if (states.length > 0) {
                url.searchParams.set('states', states.join(','));
            } else {
                url.searchParams.delete('states');
            }

            if (types.length > 0) {
                url.searchParams.set('types', types.join(','));
            } else {
                url.searchParams.delete('types');
            }

            if (page && page > 1) {
                url.searchParams.set('paged', page);
            } else {
                url.searchParams.delete('paged');
            }

            // Update browser history
            const state = { states: states, types: types, page: page };
            history.pushState(state, '', url.toString());
        },

        // Initialize scroll detection for projects grid position
        initScrollDetection: function () {
            // Check if elements exist
            if (this.cache.$projectsGrid.length === 0 || this.cache.$filterButtonWrapper.length === 0) {
                return;
            }

            // Initial check
            this.checkProjectsGridPosition();

            // Trigger scroll handler to set initial state
            this.handleScroll();
        },

        // Handle scroll event with requestAnimationFrame for performance
        handleScroll: function () {
            if (this.scrollDetection.rafId) {
                cancelAnimationFrame(this.scrollDetection.rafId);
            }

            this.scrollDetection.rafId = requestAnimationFrame(() => {
                this.checkProjectsGridPosition();
            });
        },

        // Check if projects grid top is at 20% from viewport top
        // Remove is-sticky when grid bottom is 10% from viewport bottom
        checkProjectsGridPosition: function () {
            if (!this.cache.$projectsGrid.length || !this.cache.$filterButtonWrapper.length) {
                return;
            }

            const viewportHeight = window.innerHeight;
            const isMobile = window.innerWidth <= 1198; // Match mobile breakpoint from CSS
            
            let shouldBeSticky;
            
            if (isMobile) {
                // Mobile: Check if first image is 10% from bottom
                shouldBeSticky = this.shouldShowStickyMobile(viewportHeight);
            } else {
                // Desktop: Check if grid top is 20% from top
                const gridRect = this.cache.$projectsGrid[0].getBoundingClientRect();
                shouldBeSticky = this.shouldShowStickyDesktop(gridRect, viewportHeight);
            }

            this.toggleStickyClass(shouldBeSticky);
        },

        // Determine if sticky class should be active for desktop (grid top at 20% from top)
        shouldShowStickyDesktop: function (gridRect, viewportHeight) {
            const topThreshold = viewportHeight * 0.2; // 20% from top
            const bottomThreshold = viewportHeight * 0.9; // 10% from bottom

            // Remove sticky if grid bottom reaches 10% from bottom
            if (gridRect.bottom <= bottomThreshold) {
                return false;
            }

            // Add sticky if grid top reaches 20% from top
            return gridRect.top <= topThreshold;
        },

        // Determine if sticky class should be active for mobile (first image threshold from bottom)
        shouldShowStickyMobile: function (viewportHeight) {
            const gridRect = this.cache.$projectsGrid[0].getBoundingClientRect();
            const gridBottomThreshold = viewportHeight * 0.9; // 10% from bottom (90% from top)
            
            // Remove sticky if grid bottom reaches 10% from bottom
            if (gridRect.bottom <= gridBottomThreshold) {
                return false;
            }
            
            // Find the first image/figure element in the projects grid
            const $firstImage = this.cache.$projectsGrid.find('.projects-grid__figure').first();
            
            if (!$firstImage.length) {
                // Fallback: if no figure found, check for img directly
                const $firstImg = this.cache.$projectsGrid.find('img').first();
                if (!$firstImg.length) {
                    return false;
                }
                
                const imgRect = $firstImg[0].getBoundingClientRect();
                const bottomThreshold = viewportHeight * 0.85; // 15% from bottom (85% from top)
                
                // Add sticky when first image bottom reaches 15% from viewport bottom
                // Keep sticky until grid bottom reaches 10% from bottom
                return imgRect.bottom <= bottomThreshold;
            }
            
            const figureRect = $firstImage[0].getBoundingClientRect();
            const bottomThreshold = viewportHeight * 0.85; // 15% from bottom (85% from top)
            
            // Add sticky when first image bottom reaches 15% from viewport bottom
            // Keep sticky until grid bottom reaches 10% from bottom
            return figureRect.bottom <= bottomThreshold;
        },

        // Toggle sticky class based on state
        toggleStickyClass: function (shouldBeActive) {
            const $wrapper = this.cache.$filterButtonWrapper;
            const isCurrentlyActive = this.scrollDetection.isActive;

            if (shouldBeActive && !isCurrentlyActive) {
                $wrapper.addClass('is-sticky');
                this.scrollDetection.isActive = true;
            } else if (!shouldBeActive && isCurrentlyActive) {
                $wrapper.removeClass('is-sticky');
                this.scrollDetection.isActive = false;
            }
        },

        // Update resize-font class on filter tags wrapper based on count
        updateFilterTagsResize: function () {
            const $wrapper = $('.projects-listing__filter-tags-wrapper');
            
            if (!$wrapper.length) {
                return;
            }
            
            // Count the number of filter tag elements
            const tagCount = $wrapper.find('.projects-listing__filter-tag').length;
            
            // Add resize-font class if 3 or more tags, remove otherwise
            if (tagCount >= 3) {
                $wrapper.addClass('resize-font');
            } else {
                $wrapper.removeClass('resize-font');
            }
        },

        // Trigger dot falling animation when dropdown opens
        triggerDotAnimation: function () {
            const $dots = this.cache.$filterDropdown.find('.js-filter-dot');
            
            if (!$dots.length) {
                return;
            }

            // Remove animation class to reset
            $dots.removeClass('is-animating');
            
            // Force reflow to ensure class removal is processed
            void this.cache.$filterDropdown[0].offsetHeight;
            
            // Small delay to ensure dropdown is fully visible before animation starts
            setTimeout(() => {
                $dots.addClass('is-animating');
            }, 50);
        }
    };

    // Initialize when document is ready
    $(document).ready(function () {
        // Only initialize if we're on a page with project filters
        if ($('.projects-listing-container').length > 0) {
            ProjectFilters.init();
        }
    });

    // Make ProjectFilters available globally for debugging
    window.GoldenTemplateProjectFilters = ProjectFilters;

})(jQuery);
