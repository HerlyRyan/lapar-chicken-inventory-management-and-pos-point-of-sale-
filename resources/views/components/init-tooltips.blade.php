@once
@push('scripts')
<script>
(function() {
  if (!window.initTooltips) {
    window.initTooltips = function(root) {
      try {
        var container = root || document;
        if (window.bootstrap && bootstrap.Tooltip) {
          container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            var existing = bootstrap.Tooltip.getInstance(el);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(el);
          });
        }
      } catch (e) {
        // no-op
      }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() { window.initTooltips(); });
    } else {
      window.initTooltips();
    }

    // Re-init when any Bootstrap modal is shown (helps for dynamically injected content)
    document.addEventListener('shown.bs.modal', function(e) {
      try { window.initTooltips(e.target); } catch (_) {}
    });
  }
})();
</script>
@endpush
@endonce
