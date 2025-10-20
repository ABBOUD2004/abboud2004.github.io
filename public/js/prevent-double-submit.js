/**
 * ============================================
 * PREVENT DOUBLE SUBMIT - Enhanced Version
 * ============================================
 * Prevents accidental double-click submit across forms
 * Features:
 * - Visual feedback during submission
 * - Automatic timeout reset
 * - Button state management
 * - Custom event support
 */

(function() {
  'use strict';

  // Configuration
  const CONFIG = {
    GRACE_PERIOD: 8000, // 8 seconds
    BUTTON_TEXT_LOADING: 'Processing...',
    BUTTON_TEXT_ORIGINAL: null
  };

  /**
   * Prevent double form submission
   */
  document.addEventListener('submit', function(e) {
    const form = e.target;
    
    // Only process HTMLFormElement with the data attribute
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-prevent-double-submit')) return;

    // If a submit is already in-flight, block it
    if (form.dataset.submitting === 'true') {
      console.warn('⚠️ Form submission blocked - already processing');
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    // Mark form as submitting
    form.dataset.submitting = 'true';
    
    // Find and disable submit buttons
    const submitButtons = form.querySelectorAll('[type="submit"]');
    const primaryButton = form.querySelector('[type="submit"][data-prevent-double-click]') 
      || submitButtons[0];

    submitButtons.forEach(btn => {
      btn.disabled = true;
      btn.classList.add('submitting');
      
      // Store original text if not already stored
      if (!btn.dataset.originalText) {
        btn.dataset.originalText = btn.innerHTML;
      }
    });

    // Add loading state to primary button
    if (primaryButton && !primaryButton.querySelector('.loading-spinner')) {
      const originalText = primaryButton.innerHTML;
      primaryButton.innerHTML = `
        <span class="inline-flex items-center gap-2">
          <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>${CONFIG.BUTTON_TEXT_LOADING}</span>
        </span>
      `;
    }

    // Dispatch custom event
    form.dispatchEvent(new CustomEvent('formSubmitting', {
      detail: { form, timestamp: Date.now() }
    }));

    // Reset after grace period (for AJAX forms that don't navigate)
    const resetTimeout = setTimeout(function() {
      if (form && document.body.contains(form)) {
        resetFormState(form, submitButtons);
        console.log('✅ Form submission timeout - reset allowed');
      }
    }, CONFIG.GRACE_PERIOD);

    // Store timeout ID for potential early clearing
    form.dataset.resetTimeoutId = resetTimeout;

  }, true);

  /**
   * Reset form state
   */
  function resetFormState(form, buttons) {
    form.dataset.submitting = 'false';
    
    buttons.forEach(btn => {
      btn.disabled = false;
      btn.classList.remove('submitting');
      
      // Restore original text
      if (btn.dataset.originalText) {
        btn.innerHTML = btn.dataset.originalText;
      }
    });

    // Dispatch reset event
    form.dispatchEvent(new CustomEvent('formResetState', {
      detail: { form, timestamp: Date.now() }
    }));
  }

  /**
   * Manual reset function (can be called from outside)
   */
  window.resetFormSubmitState = function(formId) {
    const form = document.getElementById(formId);
    if (form) {
      const buttons = form.querySelectorAll('[type="submit"]');
      resetFormState(form, buttons);
      
      // Clear timeout if exists
      if (form.dataset.resetTimeoutId) {
        clearTimeout(parseInt(form.dataset.resetTimeoutId));
      }
    }
  };

  /**
   * Prevent double-click on buttons with data attribute
   */
  document.addEventListener('click', function(e) {
    const button = e.target.closest('[data-prevent-double-click]');
    if (!button) return;

    if (button.disabled || button.classList.contains('processing')) {
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    // Add temporary processing class
    button.classList.add('processing');
    
    setTimeout(function() {
      button.classList.remove('processing');
    }, 1000);
  }, true);

  console.log('✅ Double-submit prevention loaded');
})();
