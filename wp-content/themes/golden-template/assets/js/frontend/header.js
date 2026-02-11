/**
 * Header Navigation
 * 
 * @package JLBPartners
 */

(function () {
  'use strict';

  // Wait for DOM to be ready
  document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const body = document.body;
    const header = document.querySelector('.js-header');
    const mobileToggle = document.querySelector('.js-header-toggle');

    // Exit early if required elements don't exist
    if (!header || !mobileToggle) {
      return;
    }

    const mobileToggleText = mobileToggle.querySelector('.menu-text');

    let headerHeight = header.offsetHeight;
    header.style.setProperty('--header-height', `${headerHeight}px`);

    let windowWidth = window.innerWidth;
    window.addEventListener('resize', function () {
      if (windowWidth !== window.innerWidth) {
        windowWidth = window.innerWidth;
        headerHeight = header.offsetHeight;
        header.style.setProperty('--header-height', `${headerHeight}px`);
      }
    });

    // Mobile menu toggle
    mobileToggle.addEventListener('click', function () {
      header.classList.toggle('is-nav-active');
      mobileToggle.classList.toggle('is-active');
      
      const isActive = header.classList.contains('is-nav-active');
      mobileToggle.setAttribute('aria-expanded', isActive);
      
      if (mobileToggleText) {
        mobileToggleText.textContent = isActive ? 'Close' : 'Menu';
      }
      
      body.classList.toggle('menu-open');
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        header.classList.remove('is-nav-active');
        mobileToggle.classList.remove('is-active');
        mobileToggle.setAttribute('aria-expanded', false);
        if (mobileToggleText) {
          mobileToggleText.textContent = 'Menu';
        }
        body.classList.remove('menu-open');
      }
    });

    let lastScrollTop = 0;
    window.addEventListener('scroll', function () {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > lastScrollTop) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  });
})();
