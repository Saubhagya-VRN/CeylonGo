<?php
// Transport request modal – included by trip.php. Expects $tourist_data, $user_name in scope.
$modal_customer_name = $user_name ?? '';
$modal_contact = (isset($tourist_data['contact_number']) ? $tourist_data['contact_number'] : '') ?: ($_SESSION['tourist_contact'] ?? '');
$vehicle_types = [
    '' => 'Select a vehicle',
    'Private Car' => 'Private Car',
    'Minivan' => 'Minivan',
    'Bus' => 'Bus',
    'Train' => 'Train',
    'Transfer Service' => 'Transfer Service',
    'Motorcycle' => 'Motorcycle'
];
?>
<div class="trip-modal-overlay" id="transportRequestModalOverlay" aria-hidden="true">
  <div class="trip-modal" id="transportRequestModal" role="dialog" aria-labelledby="transportRequestModalTitle" aria-modal="true">
    <header class="trip-modal-header">
      <h2 class="trip-modal-title" id="transportRequestModalTitle">Request Transport Service</h2>
      <button type="button" class="trip-modal-close" id="transportRequestModalClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </header>
    <form action="/CeylonGo/public/tourist/transport-services" method="post" class="trip-modal-form" id="transportRequestForm">
      <div class="trip-modal-form-grid">
        <div class="trip-modal-col">
          <div class="trip-modal-field">
            <label for="tr_customerName">Customer Name</label>
            <input type="text" id="tr_customerName" name="customerName" value="<?php echo htmlspecialchars($modal_customer_name); ?>" required>
          </div>
          <div class="trip-modal-field">
            <label for="tr_date">Date</label>
            <div class="trip-modal-input-wrap">
              <input type="date" id="tr_date" name="date" placeholder="mm/dd/yyyy">
              <i class="fa-regular fa-calendar trip-modal-input-icon"></i>
            </div>
          </div>
          <div class="trip-modal-field">
            <label for="tr_vehicleType">Vehicle Type</label>
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
            <div class="trip-modal-input-wrap">
              <input type="time" id="tr_pickupTime" name="pickupTime" placeholder=" --:-- -- ">
              <i class="fa-regular fa-clock trip-modal-input-icon"></i>
            </div>
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
          <div class="trip-modal-field">
            <label for="tr_pickupLocation">Pickup Location</label>
            <input type="text" id="tr_pickupLocation" name="pickupLocation" placeholder="e.g., Bandaranaike Airport">
          </div>
          <div class="trip-modal-field">
            <label for="tr_dropoffLocation">Dropoff Location</label>
            <input type="text" id="tr_dropoffLocation" name="dropoffLocation" placeholder="e.g., Galle Fort">
          </div>
          <div class="trip-modal-field trip-modal-estimated-fare">
            <label for="tr_estimatedFare">Estimated Fare</label>
            <div class="trip-modal-fare-row">
              <input type="text" id="tr_estimatedFare" name="estimatedFare" value="LKR 0.00" readonly class="trip-modal-fare-input">
              <button type="button" class="trip-modal-btn-calculate" id="tr_btnCalculate">Calculate</button>
            </div>
          </div>
        </div>
      </div>
      <footer class="trip-modal-footer">
        <button type="submit" class="trip-modal-btn-confirm">Confirm Selection</button>
        <button type="button" class="trip-modal-btn-cancel" id="transportRequestModalCancel">Cancel</button>
      </footer>
    </form>
  </div>
</div>
