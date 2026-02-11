/**
 * Blog Filtering JavaScript
 *
 * @package GoldenTemplate
 */

(function ($) {
    'use strict';

    // Blog filtering functionality
    const BlogFilters = {

        // Initialize the filtering system
        init: function () {
            this.bindEvents();
            this.updateURL();
        },

        // Bind event handlers
        bindEvents: function () {
            // Filter tab clicks
            $(document).on('click', '.filter-tab', this.handleFilterClick.bind(this));

            // Pagination clicks (for AJAX pagination)
            $(document).on('click', '.blog-pagination a', this.handlePaginationClick.bind(this));

            // Handle browser back/forward buttons
            $(window).on('popstate', this.handlePopState.bind(this));
        },

        // Handle filter tab clicks
        handleFilterClick: function (e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const filter = $button.data('filter');

            // Update active state
            $('.filter-tab').removeClass('active');
            $button.addClass('active');

            // Load filtered posts
            this.loadPosts(filter, 1);

            // Update URL without page reload
            this.updateURL(filter, 1);
        },

        // Handle pagination clicks
        handlePaginationClick: function (e) {
            e.preventDefault();

            const $link = $(e.currentTarget);
            const url = new URL($link.attr('href'));
            const page = url.searchParams.get('paged') || 1;
            const filter = url.searchParams.get('filter') || 'all';

            // Load posts for the requested page
            this.loadPosts(filter, page);

            // Update URL
            this.updateURL(filter, page);

            // Scroll to top of posts listing
            $('html, body').animate({
                scrollTop: $('.posts-listing-container').offset().top - 100
            }, 500);
        },

        // Handle browser back/forward navigation
        handlePopState: function (e) {
            const state = e.originalEvent.state;
            if (state && state.filter !== undefined && state.page !== undefined) {
                // Update filter tabs
                $('.filter-tab').removeClass('active');
                $('.filter-tab[data-filter="' + state.filter + '"]').addClass('active');

                // Load posts without updating URL (to avoid infinite loop)
                this.loadPosts(state.filter, state.page, false);
            }
        },

        // Load posts via AJAX
        loadPosts: function (filter, page, updateHistory = true) {
            const $container = $('.posts-listing-container');
            const $loading = $('.loading-indicator');

            // Show loading indicator
            $container.addClass('loading');
            if ($loading.length) {
                $loading.show();
            }

            // AJAX request
            $.ajax({
                url: golden_templateData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'filter_blog_posts',
                    filter: filter,
                    page: page,
                    nonce: golden_templateData.nonce
                },
                success: function (response) {
                    if (response.success) {
                        // Update posts container
                        $container.find('.posts-grid, .no-posts-found').remove();
                        $container.append(response.data.posts_html);

                        // Update pagination
                        $('.blog-pagination').html(response.data.pagination_html);

                        // Update results count if element exists
                        $('.results-count').text(response.data.total_posts + ' posts found');

                        // Hide loading indicator
                        $container.removeClass('loading');
                        if ($loading.length) {
                            $loading.hide();
                        }

                        // Update browser history
                        if (updateHistory) {
                            BlogFilters.updateURL(filter, page);
                        }

                        // Trigger custom event for other scripts
                        $(document).trigger('blog_posts_loaded', [response.data]);

                    } else {
                        console.error('AJAX request failed:', response);
                        $container.removeClass('loading');
                        if ($loading.length) {
                            $loading.hide();
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                    $container.removeClass('loading');
                    if ($loading.length) {
                        $loading.hide();
                    }

                    // Show error message
                    $container.find('.posts-grid, .no-posts-found').remove();
                    $container.append('<div class="ajax-error"><p>Sorry, there was an error loading the posts. Please try again.</p></div>');
                }
            });
        },

        // Update URL and browser history
        updateURL: function (filter = 'all', page = 1) {
            // Sanitize inputs before using in URL
            filter = filter ? filter.replace(/[<>&"']/g, '') : 'all';
            page = Math.max(1, parseInt(page)) || 1;
            const url = new URL(document.location.href);

            // Update URL parameters
            if (filter && filter !== 'all') {
                url.searchParams.set('filter', filter);
            } else {
                url.searchParams.delete('filter');
            }

            if (page && page > 1) {
                url.searchParams.set('paged', page);
            } else {
                url.searchParams.delete('paged');
            }

            // Update browser history
            const state = { filter: filter, page: page };
            history.pushState(state, '', url.toString());
        },

        // Get current filter from URL
        getCurrentFilter: function () {
            const url = new URL(document.location.href);
            const filter = url.searchParams.get('filter');
            // Sanitize filter value to prevent XSS
            return filter ? filter.replace(/[<>&"']/g, '') : 'all';
        },

        // Get current page from URL
        getCurrentPage: function () {
            const url = new URL(document.location.href);
            const page = url.searchParams.get('paged');
            // Ensure page is a valid positive integer
            return page ? Math.max(1, parseInt(page)) || 1 : 1;
        }
    };

    // Initialize when document is ready
    $(document).ready(function () {
        // Only initialize if we're on a page with blog filters
        if ($('.filter-tab').length > 0) {
            BlogFilters.init();

            // Set initial active state based on URL
            const currentFilter = BlogFilters.getCurrentFilter();
            $('.filter-tab').removeClass('active');
            $('.filter-tab[data-filter="' + currentFilter + '"]').addClass('active');
        }
    });

    // Make BlogFilters available globally for debugging
    window.GoldenTemplateBlogFilters = BlogFilters;

})(jQuery);