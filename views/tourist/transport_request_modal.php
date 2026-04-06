<?php
// Transport request modal – included by trip.php. Expects $tourist_data, $user_name in scope.
$modal_customer_name = $user_name ?? '';
$modal_contact = (isset($tourist_data['contact_number']) ? $tourist_data['contact_number'] : '') ?: ($_SESSION['tourist_contact'] ?? '');
$vehicle_types = [
    '' => 'Select a vehicle',
    'Tuk' => 'Tuk (3 people)',
    'Car' => 'Car (4 people)',
    'Minivan' => 'Minivan (7 people)',
    'Minivan AC' => 'Minivan AC (7 people)',
    'Bus' => 'Bus (20 people)',
    'Bus AC' => 'Bus AC (20 people)'
];
?>
<div class="trip-modal-overlay" id="transportRequestModalOverlay" aria-hidden="true">
  <div class="trip-modal" id="transportRequestModal" role="dialog" aria-labelledby="transportRequestModalTitle" aria-modal="true">
    <header class="trip-modal-header">
      <h2 class="trip-modal-title" id="transportRequestModalTitle">Request Transport Service</h2>
      <button type="button" class="trip-modal-close" id="transportRequestModalClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </header>
    <div id="tr_formWrap" class="trip-modal-form-wrap">
    <form action="/CeylonGo/public/tourist/transport-services" method="post" class="trip-modal-form" id="transportRequestForm">
      <div class="trip-modal-form-grid">
        <div class="trip-modal-col">
          <div class="trip-modal-field">
            <label for="tr_customerName">Customer Name</label>
            <input type="text" id="tr_customerName" name="customerName" value="<?php echo htmlspecialchars($modal_customer_name); ?>" required>
          </div>
          <div class="trip-modal-field">
            <label for="tr_date">Date</label>
            <input type="date" id="tr_date" name="date" placeholder="mm/dd/yyyy" required>
          </div>
          <div class="trip-modal-field">
            <label for="tr_vehicleType">Vehicle Type</label>
            <p class="trip-modal-field-error" id="tr_vehicleError" role="alert" aria-live="polite" style="display:none; color:#c62828; font-size:13px; margin:4px 0 0 0;"></p>
            <div class="trip-modal-input-wrap trip-modal-select-wrap">
              <select id="tr_vehicleType" name="vehicleType" required>
                <?php foreach ($vehicle_types as $val => $label): ?>
                  <option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($label); ?></option>
                <?php endforeach; ?>
              </select>
              <i class="fa-solid fa-chevron-down trip-modal-input-icon"></i>
            </div>
          </div>
          <div class="trip-modal-field">
            <label for="tr_pickupTime">Pickup Time</label>
            <input type="time" id="tr_pickupTime" name="pickupTime" placeholder=" --:-- -- " required>
          </div>
          <div class="trip-modal-field">
            <label for="tr_notes">Notes (optional)</label>
            <input type="text" id="tr_notes" name="notes" placeholder="Any extra details">
          </div>
        </div>
        <div class="trip-modal-col">
          <div class="trip-modal-field">
            <label for="tr_contactNumber">Contact Number</label>
            <input type="text" id="tr_contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($modal_contact); ?>" required>
          </div>
          <div class="trip-modal-field">
            <label for="tr_numPeople">No. of People</label>
            <input type="number" id="tr_numPeople" name="numPeople" min="1" value="1" required>
          </div>
          <div class="trip-modal-field trip-modal-location-field">
            <label for="tr_pickupLocation">Pickup Location</label>
            <div class="trip-modal-location-wrap">
              <i class="fa-solid fa-map-pin trip-modal-location-icon"></i>
              <input type="text" id="tr_pickupLocation" name="pickupLocation" placeholder="Bandaranaike Airport" autocomplete="off" required>
            </div>
          </div>
          <div class="trip-modal-field trip-modal-location-field">
            <label for="tr_dropoffLocation">Dropoff Location</label>
            <div class="trip-modal-location-wrap">
              <i class="fa-solid fa-map-pin trip-modal-location-icon"></i>
              <input type="text" id="tr_dropoffLocation" name="dropoffLocation" placeholder="Galle Fort" autocomplete="off" required>
            </div>
          </div>
          <div class="trip-modal-field trip-modal-estimated-fare">
            <label for="tr_estimatedFare">Estimated Fare</label>
            <div class="trip-modal-fare-row">
              <input type="text" id="tr_estimatedFare" value="LKR 0.00" readonly class="trip-modal-fare-input">
              <input type="hidden" id="tr_estimatedFareValue" name="estimatedFare" value="">
              <input type="hidden" id="tr_distanceValue" name="distance" value="">
              <button type="button" class="trip-modal-btn-calculate" id="tr_btnCalculate">Calculate</button>
            </div>
          </div>
        </div>
      </div>
      <div class="trip-modal-fare-breakdown" id="tr_fareBreakdown" style="display:none;">
        <div class="trip-modal-fare-breakdown-row"><span class="trip-modal-fare-label">Distance:</span> <span id="tr_fareDistance">—</span></div>
        <div class="trip-modal-fare-breakdown-row"><span class="trip-modal-fare-label">Base Rate:</span> <span id="tr_fareBaseRate">—</span></div>
        <div class="trip-modal-fare-breakdown-row"><span class="trip-modal-fare-label">Total Fare:</span> <span id="tr_fareTotal" class="trip-modal-fare-total">—</span></div>
      </div>
      <footer class="trip-modal-footer">
        <button type="submit" class="trip-modal-btn-confirm" id="tr_btnConfirm" disabled>Confirm Selection</button>
        <button type="button" class="trip-modal-btn-cancel" id="transportRequestModalCancel">Cancel</button>
      </footer>
    </form>
    </div>
    <div id="tr_successState" class="trip-modal-success-state" style="display:none; padding:32px 24px; text-align:center;">
      <p class="trip-modal-success-message" style="font-size:18px; color:#4a7c59; margin:0 0 20px 0;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Request submitted!</p>
      <p style="font-size:14px; color:#555; margin:0 0 24px 0;">Need another vehicle for the same trip?</p>
      <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <button type="button" class="trip-modal-btn-confirm" id="tr_btnAddAnother">Add another vehicle</button>
        <button type="button" class="trip-modal-btn-cancel" id="tr_btnDone">Done</button>
      </div>
    </div>
  </div>
</div>
