<?php
// Tour guide request modal – included by trip.php. Expects $tourist_data, $user_name in scope.
$guide_modal_customer_name = $user_name ?? '';
$guide_modal_contact = (isset($tourist_data['contact_number']) ? $tourist_data['contact_number'] : '') ?: ($_SESSION['tourist_contact'] ?? '');
$languages = [
    '' => 'Select language',
    'English' => 'English',
    'Sinhala' => 'Sinhala',
    'Tamil' => 'Tamil',
    'Hindi' => 'Hindi',
    'French' => 'French'
];
$time_slots = ['', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
?>
<div class="trip-modal-overlay" id="tourGuideRequestModalOverlay" aria-hidden="true">
  <div class="trip-modal" id="tourGuideRequestModal" role="dialog" aria-labelledby="tourGuideRequestModalTitle" aria-modal="true">
    <header class="trip-modal-header">
      <h2 class="trip-modal-title" id="tourGuideRequestModalTitle">Tour Guide Request</h2>
      <button type="button" class="trip-modal-close" id="tourGuideRequestModalClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </header>
    <form action="/CeylonGo/public/tourist/tour-guide-submit" method="post" class="trip-modal-form" id="tourGuideRequestForm">
      <p class="trip-modal-instruction">Provide your preferences to request a tour guide.</p>
      <div class="trip-modal-form-grid">
        <div class="trip-modal-col">
          <div class="trip-modal-field">
            <label for="tg_customerName">Customer Name</label>
            <input type="text" id="tg_customerName" name="customerName" value="<?php echo htmlspecialchars($guide_modal_customer_name); ?>" required>
          </div>
          <div class="trip-modal-field">
            <label for="tg_location">Location</label>
            <input type="text" id="tg_location" name="location" placeholder="e.g., Kandy" required>
          </div>
          <div class="trip-modal-field">
            <label for="tg_date">Preferred Date</label>
            <div class="trip-modal-input-wrap">
              <input type="date" id="tg_date" name="date" required>
              <i class="fa-regular fa-calendar trip-modal-input-icon"></i>
            </div>
          </div>
        </div>
        <div class="trip-modal-col">
          <div class="trip-modal-field">
            <label for="tg_contact">Contact Number</label>
            <input type="text" id="tg_contact" name="contact" value="<?php echo htmlspecialchars($guide_modal_contact); ?>" required>
          </div>
          <div class="trip-modal-field">
            <label for="tg_language">Preferred Language</label>
            <div class="trip-modal-input-wrap trip-modal-select-wrap">
              <select id="tg_language" name="language" required>
                <?php foreach ($languages as $val => $label): ?>
                  <option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($label); ?></option>
                <?php endforeach; ?>
              </select>
              <i class="fa-solid fa-chevron-down trip-modal-input-icon"></i>
            </div>
          </div>
          <div class="trip-modal-field">
            <label for="tg_time">Preferred Time</label>
            <div class="trip-modal-input-wrap trip-modal-select-wrap">
              <select id="tg_time" name="time" required>
                <option value="">Select time</option>
                <?php for ($i = 1; $i < count($time_slots); $i++): ?>
                  <option value="<?php echo htmlspecialchars($time_slots[$i]); ?>"><?php echo htmlspecialchars($time_slots[$i]); ?></option>
                <?php endfor; ?>
              </select>
              <i class="fa-solid fa-chevron-down trip-modal-input-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="trip-modal-field trip-modal-notes">
        <label for="tg_notes">Notes (optional)</label>
        <textarea id="tg_notes" name="notes" rows="3" placeholder="Any special requests"></textarea>
      </div>
      <footer class="trip-modal-footer">
        <button type="submit" class="trip-modal-btn-confirm">Submit Request</button>
        <button type="button" class="trip-modal-btn-cancel" id="tourGuideRequestModalCancel">Cancel</button>
      </footer>
    </form>
  </div>
</div>
