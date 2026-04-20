<div class="trip-modal-overlay trip-modal-overlay--trip-overview" id="tripBudgetSummaryModalOverlay" aria-hidden="true">
  <div class="trip-modal trip-modal--budget-summary" role="dialog" aria-labelledby="tripBudgetSummaryModalTitle" aria-modal="true">
    <header class="trip-modal-header">
      <h2 class="trip-modal-title" id="tripBudgetSummaryModalTitle">Trip Summary</h2>
      <div class="trip-modal-header-actions">
        <button type="button" class="trip-modal-download-btn" id="tripBudgetSummaryModalDownload" title="Download trip summary as an HTML file">
          <i class="fa-solid fa-download" aria-hidden="true"></i><span>Download</span>
        </button>
        <button type="button" class="trip-modal-close" id="tripBudgetSummaryModalClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </header>
    <div class="trip-modal-budget-body trip-summary-detailed-root" id="tripBudgetSummaryModalMount" aria-live="polite"></div>
  </div>
</div>

<?php include __DIR__ . '/_trip_service_provider_modal.php'; ?>

<div id="tripCustomRefundModal" class="refund-modal trip-refund-modal" hidden aria-hidden="true">
  <div class="refund-modal__backdrop js-trip-custom-refund-close" tabindex="-1"></div>
  <div class="refund-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tripCustomRefundModalTitle">
    <div class="refund-modal__head">
      <h2 id="tripCustomRefundModalTitle" class="refund-modal__title">Refund</h2>
      <button type="button" class="refund-modal__close js-trip-custom-refund-close" aria-label="Close">&times;</button>
    </div>
    <div class="refund-modal__body" id="tripCustomRefundModalBody"></div>
  </div>
</div>
