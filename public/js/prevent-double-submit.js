(function(){
  'use strict';

  // Prevent accidental double-click submit across forms opting in via data attributes
  document.addEventListener('submit', function(e){
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-prevent-double-submit')) return;

    // If a submit is already in-flight, block
    if (form.dataset.submitting === 'true') {
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    form.dataset.submitting = 'true';

    // Try to disable the primary submit button immediately
    const submitBtn = form.querySelector('[type="submit"][data-prevent-double-click]') || form.querySelector('[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
    }

    // If the page does not navigate (e.g., AJAX), allow re-submission after a grace period unless caller manages it.
    setTimeout(function(){
      if (form && document.body.contains(form)) {
        form.dataset.submitting = 'false';
        if (submitBtn) submitBtn.disabled = false;
      }
    }, 8000);
  }, true);
})();
