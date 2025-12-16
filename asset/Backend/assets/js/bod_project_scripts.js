// Common BOD project scripts
// Minimal safe stub so components depending on bindTableEvents won't error.
(function(window, document){
  'use strict';

  // Public function referenced by DataTables drawCallback
  window.bindTableEvents = function() {
    // Example: attach handlers for elements that need re-binding after DataTable redraw
    // e.g., confirm modals or custom buttons
    // Currently no-op, add handlers here as needed.

    // Safe debug when in development
    if (window.console && console.debug) console.debug('bindTableEvents() called');
  };

  // Example of additional helpers (expand as needed)
  window.bodHelpers = {
    goToHref: function(el) {
      if (!el) return;
      var href = el.getAttribute('href');
      if (href) { window.location.href = href; }
    }
  };

})(window, document);
