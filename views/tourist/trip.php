<?php
$user_name = isset($user_name) ? $user_name : '';
$tourist_data = isset($tourist_data) ? $tourist_data : null;
$places_autocomplete_url = isset($places_autocomplete_url) ? $places_autocomplete_url : '/CeylonGo/public/api/places-autocomplete';
$user_email = '';
if (is_array($tourist_data) && isset($tourist_data['email'])) {
    $user_email = $tourist_data['email'];
} elseif (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
}
$payhere_per_transaction_max_lkr = isset($payhere_per_transaction_max_lkr) ? (int) $payhere_per_transaction_max_lkr : 0;
$bank_transfer_details = isset($bank_transfer_details) ? $bank_transfer_details : '';
$last_trip_id = isset($last_trip_id) ? (int) $last_trip_id : 0;
$wizard_fresh_start = !empty($wizard_fresh_start);
$trip_summary_embed = isset($_GET['summary_embed']) && (string) $_GET['summary_embed'] === '1';
$avatar_initial = $user_name ? strtoupper(substr(trim($user_name), 0, 1)) : 'T';
$asset_base = '/CeylonGo/public';
$user_email_sidebar = $user_email;
$trip_sidebar_active = 'customize';
$steps = array(
    'Travel Group',
    'Destination & Dates',
    'Accommodation',
    'Another Destination?',
    'Second Destination',
    'Second Stay',
    'Another Destination?',
    'Third Destination',
    'Third Stay',
    'Budget',
    'Trip Review & Submit',
    'Verify bookings',
    'Payments',
    'Trip Overview'
);
$districts = array(
    'ampara' => 'Ampara', 'anuradhapura' => 'Anuradhapura', 'badulla' => 'Badulla', 'batticaloa' => 'Batticaloa',
    'colombo' => 'Colombo', 'galle' => 'Galle', 'gampaha' => 'Gampaha', 'hambantota' => 'Hambantota',
    'jaffna' => 'Jaffna', 'kalutara' => 'Kalutara', 'kandy' => 'Kandy', 'kegalle' => 'Kegalle',
    'kilinochchi' => 'Kilinochchi', 'kurunegala' => 'Kurunegala', 'mannar' => 'Mannar', 'matale' => 'Matale',
    'matara' => 'Matara', 'monaragala' => 'Monaragala', 'mullaitivu' => 'Mullaitivu', 'nuwara-eliya' => 'Nuwara Eliya',
    'polonnaruwa' => 'Polonnaruwa', 'puttalam' => 'Puttalam', 'ratnapura' => 'Ratnapura',
    'trincomalee' => 'Trincomalee', 'vavuniya' => 'Vavuniya'
);
// Main cities for destination autocomplete (display name => district key for form/API)
$main_cities = array(
    'Colombo' => 'colombo', 'Kandy' => 'kandy', 'Galle' => 'galle', 'Negombo' => 'gampaha', 'Jaffna' => 'jaffna',
    'Trincomalee' => 'trincomalee', 'Batticaloa' => 'batticaloa', 'Matara' => 'matara', 'Anuradhapura' => 'anuradhapura',
    'Nuwara Eliya' => 'nuwara-eliya', 'Bentota' => 'kalutara', 'Ella' => 'badulla', 'Sigiriya' => 'matale',
    'Dambulla' => 'matale', 'Hikkaduwa' => 'galle', 'Mirissa' => 'matara', 'Arugam Bay' => 'ampara',
    'Unawatuna' => 'galle', 'Ratnapura' => 'ratnapura', 'Badulla' => 'badulla', 'Kurunegala' => 'kurunegala',
    'Gampaha' => 'gampaha', 'Kalutara' => 'kalutara', 'Hambantota' => 'hambantota', 'Polonnaruwa' => 'polonnaruwa',
    'Ampara' => 'ampara', 'Matale' => 'matale', 'Kegalle' => 'kegalle', 'Puttalam' => 'puttalam',
    'Vavuniya' => 'vavuniya', 'Mannar' => 'mannar', 'Kilinochchi' => 'kilinochchi', 'Mullaitivu' => 'mullaitivu',
    'Monaragala' => 'monaragala', 'Mount Lavinia' => 'colombo', 'Galle Fort' => 'galle', 'Yala' => 'hambantota'
);
?>
<!DOCTYPE html>
<html lang="en"<?php echo !empty($trip_summary_embed) ? ' class="trip-summary-embed-html"' : ''; ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!empty($trip_summary_embed)): ?>
    <style id="trip-summary-embed-boot">
      /* First paint inside dashboard iframe: match white modal card (no black flash) */
      html.trip-summary-embed-html, html.trip-summary-embed-html body {
        margin: 0;
        background: #fff;
      }
      html.trip-summary-embed-html body > header.navbar,
      html.trip-summary-embed-html body > .sidebar-overlay,
      html.trip-summary-embed-html body > .trip-page-wrapper {
        visibility: hidden !important;
        position: absolute !important;
        left: -99999px !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
        pointer-events: none !important;
      }
    </style>
    <?php endif; ?>
    <title>Customise Your Trip - Ceylon Go</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip_layout.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/accommodation_content.css">
</head>
<body class="trip-page-body<?php echo $trip_summary_embed ? ' trip-summary-embed' : ''; ?>"<?php echo $wizard_fresh_start ? ' data-wizard-fresh="1"' : ''; ?>>
  <?php include __DIR__ . '/header.php'; ?>

  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>

    <main class="trip-main-content">
      <button class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="/CeylonGo/public/tourist/dashboard-side"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>customise-your-trip</span>
      </div>

      <div class="trip-header-row">
        <div class="trip-stepper-prev">
          <button type="button" class="btn-prev" disabled><i class="fa-solid fa-chevron-left"></i> Previous</button>
        </div>
        <h1 class="trip-page-title trip-title-centered">
          <i class="fa-solid fa-wand-magic-sparkles"></i> Customise Your Dream Trip
        </h1>
        <div class="trip-stepper-next">
          <div class="trip-stepper-next-group">
            <button type="button" class="btn-trip-clear-data" id="tripClearDataBtn" title="Clear saved draft on this device and return to Travel Group">Clear data</button>
            <button type="button" class="btn-next">Next <i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
      </div>

      <div class="trip-stepper-steps-wrap">
        <div class="trip-stepper-steps" id="tripStepperSteps">
          <?php foreach ($steps as $i => $label):
            $stepNum = $i + 1;
            $isSecondLeg = ($stepNum === 5 || $stepNum === 6);
            $isThirdPrompt = ($stepNum === 7);
            $isThirdLeg = ($stepNum === 8 || $stepNum === 9);
            $stepClasses = array('trip-step');
            if ($isSecondLeg) {
                $stepClasses[] = 'trip-step--second-leg';
                $stepClasses[] = 'trip-step--second-leg-hidden';
            }
            if ($isThirdPrompt) {
                $stepClasses[] = 'trip-step--third-prompt';
                $stepClasses[] = 'trip-step--third-prompt-hidden';
            }
            if ($isThirdLeg) {
                $stepClasses[] = 'trip-step--third-leg';
                $stepClasses[] = 'trip-step--third-leg-hidden';
            }
            ?>
            <div class="<?php echo htmlspecialchars(implode(' ', $stepClasses)); ?>" data-step="<?php echo (int) $stepNum; ?>"<?php
              if ($isSecondLeg) {
                  echo ' data-second-leg="1" aria-hidden="true"';
              }
              if ($isThirdPrompt) {
                  echo ' data-third-prompt="1" aria-hidden="true"';
              }
              if ($isThirdLeg) {
                  echo ' data-third-leg-flow="1" aria-hidden="true"';
              }
            ?>>
              <?php if ($i > 0): ?><div class="trip-step-line"></div><?php endif; ?>
              <span class="trip-step-label"><?php echo htmlspecialchars($label); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="trip-step-panel active" data-step="1">
      <div class="trip-step-card trip-step-card--travel-group">
        <div class="step-icon step-icon--travel-group"><i class="fa-solid fa-people-group"></i></div>
        <h2 class="step-heading">Who's traveling with you?</h2>
        <p class="step-subheading">Tell us about your travel companions</p>
        <div class="trip-travel-group-form">
          <div class="trip-counters-row">
            <div class="trip-counter-group">
              <label class="trip-counter-label"><i class="trip-counter-icon fa-solid fa-person"></i> Adults (12+ years)</label>
              <div class="trip-counter">
                <button type="button" class="trip-counter-btn" data-target="adults" data-dir="-1" aria-label="Decrease">−</button>
                <input type="number" id="adults" name="adults" value="1" min="1" max="50" readonly class="trip-counter-value">
                <button type="button" class="trip-counter-btn" data-target="adults" data-dir="1" aria-label="Increase">+</button>
              </div>
            </div>
            <div class="trip-counter-group">
              <label class="trip-counter-label"><i class="trip-counter-icon fa-solid fa-child"></i> Children (2–11 years)</label>
              <div class="trip-counter">
                <button type="button" class="trip-counter-btn" data-target="children" data-dir="-1">−</button>
                <input type="number" id="children" name="children" value="0" min="0" max="50" readonly class="trip-counter-value">
                <button type="button" class="trip-counter-btn" data-target="children" data-dir="1">+</button>
              </div>
            </div>
            <div class="trip-counter-group">
              <label class="trip-counter-label"><i class="trip-counter-icon fa-solid fa-baby"></i> Infants (0–2 years)</label>
              <div class="trip-counter">
                <button type="button" class="trip-counter-btn" data-target="infants" data-dir="-1">−</button>
                <input type="number" id="infants" name="infants" value="0" min="0" max="50" readonly class="trip-counter-value">
                <button type="button" class="trip-counter-btn" data-target="infants" data-dir="1">+</button>
              </div>
            </div>
          </div>
          <div class="trip-type-section">
            <h3 class="trip-type-heading">Type of Trip</h3>
            <div class="trip-type-options trip-type-cards">
              <button type="button" class="trip-type-card" data-type="couple">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Couple</span>
              </button>
              <button type="button" class="trip-type-card" data-type="family">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Family</span>
              </button>
              <button type="button" class="trip-type-card" data-type="friends">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Friends</span>
              </button>
              <button type="button" class="trip-type-card" data-type="solo">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Solo</span>
              </button>
            </div>
            <input type="hidden" name="trip_type" id="trip_type" value="">
            <p class="trip-date-error trip-type-error" id="tripTypeError" role="alert" aria-live="polite"></p>
          </div>
        </div>
      </div>
      </div>

      <div class="trip-step-panel" data-step="2">
      <div class="trip-step-card trip-step-card--dest-dates trip-wireframe-card">
        <div class="step-icon step-icon--dest-dates"><i class="fa-solid fa-location-dot"></i></div>
        <h2 class="trip-section-heading">Select Destination</h2>

        <form method="POST" action="#" id="trip-step-form">
          <div class="form-row trip-dates-row dest-dates-row trip-dest-row-single">
            <div class="form-group trip-date-group trip-dest-group">
              <label for="dest_primary">Destination</label>
              <div class="input-with-icon input-with-icon--dest trip-dest-input-wrap">
                <i class="fa-solid fa-location-dot input-icon input-icon--left"></i>
                <input type="text" id="dest_primary" name="destinations[0][name]" value="" placeholder="Type or select a city" autocomplete="off" aria-autocomplete="list" aria-controls="dest_suggestions" aria-expanded="false">
                <input type="hidden" id="dest_district_hidden" name="destinations[0][district]" value="">
                <i class="fa-solid fa-chevron-down input-icon input-icon--right"></i>
                <div class="trip-dest-suggestions" id="dest_suggestions" role="listbox" aria-hidden="true"></div>
              </div>
            </div>
          </div>

          <div class="form-row trip-dates-row dest-dates-row">
            <div class="form-group trip-date-group">
              <label for="start_date">Start Date</label>
              <div class="input-with-icon input-with-icon--date">
                <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                <input type="date" id="start_date" name="start_date" value="2026-02-04" required aria-describedby="tripDateError">
              </div>
            </div>
            <div class="form-group trip-date-group">
              <label for="end_date">End Date</label>
              <div class="input-with-icon input-with-icon--date">
                <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                <input type="date" id="end_date" name="end_date" value="2026-02-09" required aria-describedby="tripDateError">
              </div>
            </div>
          </div>
          <p class="trip-date-error" id="tripDateError" role="alert" aria-live="polite"></p>

          <div class="trip-duration-banner" id="tripDuration">5 Nights</div>

          <div class="trip-stops-section">
            <h3 class="trip-stops-heading" id="tripStopsHeading">Stops in this area</h3>
            <p class="trip-stops-desc">You can add many stops and request transport &amp; a tour guide if needed.</p>
            <p class="trip-date-error" id="tripStopsError" role="alert" aria-live="polite"></p>
            <div id="tripStopsList" class="trip-stops-list"></div>
            <button type="button" class="btn-add-more-stops" id="btnAddMoreStops"><i class="fa-solid fa-plus"></i> Add a stop</button>
          </div>

          <input type="hidden" name="destinations[0][days]" value="1">
        </form>
      </div>
      </div>

      <div class="trip-step-panel" data-step="3">
        <div class="trip-step-card trip-step-card--accommodation">
          <?php include __DIR__ . '/_accommodation_content.php'; ?>
        </div>
      </div>

      <div class="trip-step-panel" data-step="4">
        <div class="trip-step-card trip-step-card--another-dest">
          <div class="step-icon step-icon--another-dest"><i class="fa-solid fa-map-location-dot"></i></div>
          <h2 class="step-heading">Another Destination?</h2>
          <p class="step-subheading">Optional: add another place to your itinerary</p>
          <div class="trip-another-dest-inner">
            <p class="trip-another-dest-question">Do you want to add another destination?</p>
            <div class="trip-type-options trip-type-cards trip-another-dest-cards">
              <button type="button" class="trip-another-dest-card" id="btnAnotherDestYes" data-value="yes" aria-pressed="false">
                <i class="trip-type-card-icon fa-solid fa-check"></i>
                <span class="trip-type-card-label">Yes</span>
              </button>
              <button type="button" class="trip-another-dest-card" id="btnAnotherDestNo" data-value="no" aria-pressed="false">
                <i class="trip-type-card-icon fa-solid fa-xmark"></i>
                <span class="trip-type-card-label">No</span>
              </button>
            </div>
            <input type="hidden" id="add_another_destination" name="add_another_destination" value="">
            <p class="trip-date-error" id="tripAnotherDestError" role="alert" aria-live="polite"></p>
          </div>
        </div>
      </div>

      <div class="trip-step-panel" data-step="5">
        <div class="trip-step-card trip-step-card--dest-dates trip-wireframe-card">
          <div class="step-icon step-icon--dest-dates"><i class="fa-solid fa-location-dot"></i></div>
          <h2 class="trip-section-heading">Second destination</h2>
          <p class="step-subheading">Dates and stops for your additional place</p>
          <form method="POST" action="#" id="trip-step-form-second-dest">
            <div class="form-row trip-dates-row dest-dates-row trip-dest-row-single">
              <div class="form-group trip-date-group trip-dest-group">
                <label for="dest_primary_2">Destination</label>
                <div class="input-with-icon input-with-icon--dest trip-dest-input-wrap">
                  <i class="fa-solid fa-location-dot input-icon input-icon--left"></i>
                  <input type="text" id="dest_primary_2" name="destinations[1][name]" value="" placeholder="Type or select a city" autocomplete="off" aria-autocomplete="list" aria-controls="dest_suggestions_2" aria-expanded="false">
                  <input type="hidden" id="dest_district_hidden_2" name="destinations[1][district]" value="">
                  <i class="fa-solid fa-chevron-down input-icon input-icon--right"></i>
                  <div class="trip-dest-suggestions" id="dest_suggestions_2" role="listbox" aria-hidden="true"></div>
                </div>
              </div>
            </div>
            <div class="form-row trip-dates-row dest-dates-row">
              <div class="form-group trip-date-group">
                <label for="start_date_2">Start Date</label>
                <div class="input-with-icon input-with-icon--date">
                  <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                  <input type="date" id="start_date_2" name="start_date_2" value="" required aria-describedby="tripDateError_2">
                </div>
              </div>
              <div class="form-group trip-date-group">
                <label for="end_date_2">End Date</label>
                <div class="input-with-icon input-with-icon--date">
                  <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                  <input type="date" id="end_date_2" name="end_date_2" value="" required aria-describedby="tripDateError_2">
                </div>
              </div>
            </div>
            <p class="trip-date-error" id="tripDateError_2" role="alert" aria-live="polite"></p>
            <div class="trip-duration-banner" id="tripDuration_2">—</div>
            <div class="trip-stops-section">
              <h3 class="trip-stops-heading" id="tripStopsHeading_2">Stops in this area</h3>
              <p class="trip-stops-desc">Add stops for this destination. You can request transport and a tour guide if needed.</p>
              <p class="trip-date-error" id="tripStopsError_2" role="alert" aria-live="polite"></p>
              <div id="tripStopsList_2" class="trip-stops-list"></div>
              <button type="button" class="btn-add-more-stops" id="btnAddMoreStops_2"><i class="fa-solid fa-plus"></i> Add a stop</button>
            </div>
            <input type="hidden" name="destinations[1][days]" value="1">
          </form>
        </div>
      </div>

      <div class="trip-step-panel" data-step="6">
        <div class="trip-step-card trip-step-card--accommodation">
          <?php $trip_accommodation_block = 'secondary'; include __DIR__ . '/_accommodation_content.php'; unset($trip_accommodation_block); ?>
        </div>
      </div>

      <div class="trip-step-panel" data-step="7">
        <div class="trip-step-card trip-step-card--another-dest">
          <div class="step-icon step-icon--another-dest"><i class="fa-solid fa-map-location-dot"></i></div>
          <h2 class="step-heading">Add a third destination?</h2>
          <p class="step-subheading">You can include up to three destinations on this trip</p>
          <div class="trip-another-dest-inner">
            <p class="trip-another-dest-question">Do you want to add another destination?</p>
            <div class="trip-type-options trip-type-cards trip-another-dest-cards">
              <button type="button" class="trip-another-dest-card" id="btnThirdDestYes" data-value="yes" aria-pressed="false">
                <i class="trip-type-card-icon fa-solid fa-check"></i>
                <span class="trip-type-card-label">Yes</span>
              </button>
              <button type="button" class="trip-another-dest-card" id="btnThirdDestNo" data-value="no" aria-pressed="false">
                <i class="trip-type-card-icon fa-solid fa-xmark"></i>
                <span class="trip-type-card-label">No</span>
              </button>
            </div>
            <input type="hidden" id="add_third_destination" name="add_third_destination" value="">
            <p class="trip-date-error" id="tripThirdDestError" role="alert" aria-live="polite"></p>
          </div>
        </div>
      </div>

      <div class="trip-step-panel" data-step="8">
        <div class="trip-step-card trip-step-card--dest-dates trip-wireframe-card">
          <div class="step-icon step-icon--dest-dates"><i class="fa-solid fa-location-dot"></i></div>
          <h2 class="trip-section-heading">Third destination</h2>
          <p class="step-subheading">Dates and stops for your third place</p>
          <form method="POST" action="#" id="trip-step-form-third-dest">
            <div class="form-row trip-dates-row dest-dates-row trip-dest-row-single">
              <div class="form-group trip-date-group trip-dest-group">
                <label for="dest_primary_3">Destination</label>
                <div class="input-with-icon input-with-icon--dest trip-dest-input-wrap">
                  <i class="fa-solid fa-location-dot input-icon input-icon--left"></i>
                  <input type="text" id="dest_primary_3" name="destinations[2][name]" value="" placeholder="Type or select a city" autocomplete="off" aria-autocomplete="list" aria-controls="dest_suggestions_3" aria-expanded="false">
                  <input type="hidden" id="dest_district_hidden_3" name="destinations[2][district]" value="">
                  <i class="fa-solid fa-chevron-down input-icon input-icon--right"></i>
                  <div class="trip-dest-suggestions" id="dest_suggestions_3" role="listbox" aria-hidden="true"></div>
                </div>
              </div>
            </div>
            <div class="form-row trip-dates-row dest-dates-row">
              <div class="form-group trip-date-group">
                <label for="start_date_3">Start Date</label>
                <div class="input-with-icon input-with-icon--date">
                  <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                  <input type="date" id="start_date_3" name="start_date_3" value="" required aria-describedby="tripDateError_3">
                </div>
              </div>
              <div class="form-group trip-date-group">
                <label for="end_date_3">End Date</label>
                <div class="input-with-icon input-with-icon--date">
                  <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                  <input type="date" id="end_date_3" name="end_date_3" value="" required aria-describedby="tripDateError_3">
                </div>
              </div>
            </div>
            <p class="trip-date-error" id="tripDateError_3" role="alert" aria-live="polite"></p>
            <div class="trip-duration-banner" id="tripDuration_3">—</div>
            <div class="trip-stops-section">
              <h3 class="trip-stops-heading" id="tripStopsHeading_3">Stops in this area</h3>
              <p class="trip-stops-desc">Add stops for this destination. You can request transport and a tour guide if needed.</p>
              <p class="trip-date-error" id="tripStopsError_3" role="alert" aria-live="polite"></p>
              <div id="tripStopsList_3" class="trip-stops-list"></div>
              <button type="button" class="btn-add-more-stops" id="btnAddMoreStops_3"><i class="fa-solid fa-plus"></i> Add a stop</button>
            </div>
            <input type="hidden" name="destinations[2][days]" value="1">
          </form>
        </div>
      </div>

      <div class="trip-step-panel" data-step="9">
        <div class="trip-step-card trip-step-card--accommodation">
          <?php $trip_accommodation_block = 'tertiary'; include __DIR__ . '/_accommodation_content.php'; unset($trip_accommodation_block); ?>
        </div>
      </div>

      <div class="trip-step-panel" data-step="10">
        <div class="trip-step-card trip-step-card--summary-detailed">
          <header class="trip-sum-page-intro">
            <div class="trip-sum-page-intro-icon" aria-hidden="true"><i class="fa-solid fa-map-location-dot"></i></div>
            <div>
              <h2 class="trip-sum-page-title">Budget</h2>
              <p class="trip-sum-page-lead">Your itinerary, services, and estimated budget in one place.</p>
            </div>
          </header>
          <div id="tripSummaryDetailedMount" class="trip-summary-detailed-root" aria-live="polite"></div>
        </div>
      </div>
      <div class="trip-step-panel" data-step="11">
        <div class="trip-step-card trip-step-card--summary-detailed trip-step-card--trip-summary-itinerary">
          <header class="trip-sum-page-intro">
            <div class="trip-sum-page-intro-icon" aria-hidden="true"><i class="fa-solid fa-route"></i></div>
            <div>
              <h2 class="trip-sum-page-title">Trip Review &amp; Submit</h2>
              <p class="trip-sum-page-lead">Each card uses your real travel dates and where you are going — like a day-by-day itinerary.</p>
            </div>
          </header>
          <div id="tripItinerarySummaryMount" class="trip-summary-itinerary-root" aria-live="polite"></div>
        </div>
      </div>
      <div class="trip-step-panel" data-step="12">
        <div class="trip-step-card trip-step-card--verify-bookings">
          <header class="trip-sum-page-intro">
            <div class="trip-sum-page-intro-icon" aria-hidden="true"><i class="fa-solid fa-clipboard-list"></i></div>
            <div>
              <h2 class="trip-sum-page-title">Verify bookings</h2>
              <p class="trip-sum-page-lead">Review the current status of your transport and hotel bookings before proceeding to payment.</p>
            </div>
          </header>
          <div id="tripVerifyBookingsMount" class="trip-summary-itinerary-root" aria-live="polite"></div>
        </div>
      </div>
      <div class="trip-step-panel" data-step="13">
        <div class="trip-step-card trip-step-card--payments">
          <?php include __DIR__ . '/_trip_payment_step.php'; ?>
        </div>
      </div>
      <div class="trip-step-panel" data-step="14">
        <div class="trip-step-card trip-step-card--placeholder">
          <div class="step-icon"><i class="fa-solid fa-clipboard-check"></i></div>
          <h2 class="step-heading">Trip Overview</h2>
          <p class="step-subheading">Your final confirmation after payment.</p>
          <div id="tripFinalReviewMount" style="margin-top:16px;" aria-live="polite"></div>
        </div>
      </div>
    </main>
  </div>

  <?php include __DIR__ . '/transport_request_modal.php'; ?>
  <?php include __DIR__ . '/tour_guide_request_modal.php'; ?>
  <?php include __DIR__ . '/_trip_overview_modals.php'; ?>

  <div id="tripClearDataModal" class="trip-clear-data-modal-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tripClearDataModalTitle">
    <div class="trip-clear-data-modal" role="document">
      <h2 id="tripClearDataModalTitle" class="trip-clear-data-modal__title">Clear trip data?</h2>
      <p class="trip-clear-data-modal__text">Clear all trip details saved on this device and start again at Travel Group? This does not delete past bookings from your account.</p>
      <div class="trip-clear-data-modal__actions">
        <button type="button" class="trip-clear-data-modal__btn trip-clear-data-modal__btn--cancel" id="tripClearDataModalCancel">Cancel</button>
        <button type="button" class="trip-clear-data-modal__btn trip-clear-data-modal__btn--ok" id="tripClearDataModalOk">OK</button>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    (function bindTripClearData() {
      var clearBtn = document.getElementById('tripClearDataBtn');
      var modal = document.getElementById('tripClearDataModal');
      var modalOk = document.getElementById('tripClearDataModalOk');
      var modalCancel = document.getElementById('tripClearDataModalCancel');
      if (!clearBtn) return;
      var fallbackCustomizeUrl = <?php echo json_encode(rtrim(defined('BASE_URL') ? BASE_URL : '/CeylonGo/public', '/') . '/tourist/customize-trip'); ?>;
      var trapFocus = null;

      function closeTripClearDataModal() {
        if (!modal || !modal.classList.contains('trip-clear-data-modal-overlay--open')) return;
        modal.classList.remove('trip-clear-data-modal-overlay--open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (trapFocus) {
          document.removeEventListener('keydown', trapFocus);
          trapFocus = null;
        }
        if (clearBtn && typeof clearBtn.focus === 'function') {
          try { clearBtn.focus(); } catch (eF) {}
        }
      }

      function openTripClearDataModal() {
        if (!modal) {
          try { localStorage.removeItem('ceylonTripWizardDraftV2'); } catch (e0) {}
          try {
            sessionStorage.removeItem('ceylonTripWizardSubmitted');
            sessionStorage.removeItem('ceylonTripWizardTripId');
            sessionStorage.removeItem('ceylonTripWizardFingerprint');
            sessionStorage.removeItem('ceylonTripWizardProceededToPayment');
            sessionStorage.removeItem('ceylonTripWizardReturnToReview');
          } catch (e1) {}
          var path0 = (window.location && window.location.pathname) ? String(window.location.pathname) : '';
          var target0 = (path0 && path0.indexOf('customize-trip') !== -1) ? (path0 + '?reset=1') : (fallbackCustomizeUrl + '?reset=1');
          window.location.replace(target0);
          return;
        }
        modal.classList.add('trip-clear-data-modal-overlay--open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (modalOk && typeof modalOk.focus === 'function') {
          try { modalOk.focus(); } catch (eFo) {}
        }
        trapFocus = function (kev) {
          if (kev.key === 'Escape') {
            kev.preventDefault();
            closeTripClearDataModal();
          }
        };
        document.addEventListener('keydown', trapFocus);
      }

      function runClearAndRedirect() {
        try { localStorage.removeItem('ceylonTripWizardDraftV2'); } catch (e0) {}
        try {
          sessionStorage.removeItem('ceylonTripWizardSubmitted');
          sessionStorage.removeItem('ceylonTripWizardTripId');
          sessionStorage.removeItem('ceylonTripWizardFingerprint');
          sessionStorage.removeItem('ceylonTripWizardProceededToPayment');
          sessionStorage.removeItem('ceylonTripWizardReturnToReview');
        } catch (e1) {}
        var path = (window.location && window.location.pathname) ? String(window.location.pathname) : '';
        var target = (path && path.indexOf('customize-trip') !== -1) ? (path + '?reset=1') : (fallbackCustomizeUrl + '?reset=1');
        window.location.replace(target);
      }

      clearBtn.addEventListener('click', function (ev) {
        ev.preventDefault();
        ev.stopPropagation();
        openTripClearDataModal();
      });
      if (modalOk) modalOk.addEventListener('click', function () {
        closeTripClearDataModal();
        runClearAndRedirect();
      });
      if (modalCancel) modalCancel.addEventListener('click', closeTripClearDataModal);
      if (modal) {
        modal.addEventListener('click', function (ev) {
          if (ev.target === modal) closeTripClearDataModal();
        });
      }
    })();

    var tripPageWizardFresh = !!(document.body && document.body.getAttribute('data-wizard-fresh') === '1');
    if (tripPageWizardFresh) {
      try { document.body.removeAttribute('data-wizard-fresh'); } catch (eWf) {}
      try { localStorage.removeItem('ceylonTripWizardDraftV2'); } catch (eLS) {}
      ['ceylonTripWizardSubmitted', 'ceylonTripWizardTripId', 'ceylonTripWizardFingerprint', 'ceylonTripWizardProceededToPayment', 'ceylonTripWizardReturnToReview'].forEach(function (wk) {
        try { sessionStorage.removeItem(wk); } catch (eSS) {}
      });
    }

    var serverLastTid = <?php echo (int) $last_trip_id; ?>;
    try {
      if (!tripPageWizardFresh) {
        var paramsBoot = new URLSearchParams(window.location.search || '');
        var urlTidBoot = parseInt(paramsBoot.get('trip_id') || '0', 10) || 0;
        var afterPayBoot = paramsBoot.get('afterPayment') === '1';
        var forceSubmittedBoot = paramsBoot.get('submitted') === '1';
        if (urlTidBoot > 0) {
          sessionStorage.setItem('ceylonTripWizardTripId', String(urlTidBoot));
          if (afterPayBoot || forceSubmittedBoot) {
            sessionStorage.setItem('ceylonTripWizardSubmitted', '1');
            // Prevent "submitted" getting dropped by fingerprint mismatch when opening a previously submitted trip.
            if (forceSubmittedBoot) {
              try {
                sessionStorage.setItem('ceylonTripWizardFingerprint', tripWizardFingerprint());
              } catch (eFpBoot) {}
            }
            if (afterPayBoot) {
              sessionStorage.setItem('ceylonTripWizardProceededToPayment', String(urlTidBoot));
            }
          }
        }
      }
    } catch (eUrl) {}
    try {
      if (!tripPageWizardFresh) {
        var existingTid = String(sessionStorage.getItem('ceylonTripWizardTripId') || '').trim();
        if (!existingTid && serverLastTid > 0) {
          sessionStorage.setItem('ceylonTripWizardTripId', String(serverLastTid));
        }
      }
    } catch (e0) {}

    (function autoOpenTripSummaryEmbedEarly() {
      try {
        if (!document.body.classList.contains('trip-summary-embed')) return;
        var params = new URLSearchParams(window.location.search || '');
        if (params.get('show_summary') !== '1') return;
        var tid = String(params.get('trip_id') || '').trim();
        if (!tid && serverLastTid > 0) tid = String(serverLastTid);
        openTripBudgetSummaryModal(tid);
        window.__dashTripSummaryEmbedOpened = true;
      } catch (eEarly) {}
    })();

    var hamburger = document.getElementById('tripHamburgerBtn');
    var sidebar = document.getElementById('tripSidebar');
    var overlay = document.getElementById('tripSidebarOverlay');
    function toggleSidebar() {
      if (hamburger) hamburger.classList.toggle('active');
      if (sidebar) sidebar.classList.toggle('active');
      if (overlay) overlay.classList.toggle('active');
      document.body.style.overflow = sidebar && sidebar.classList.contains('active') ? 'hidden' : '';
    }
    function closeSidebar() {
      if (hamburger) hamburger.classList.remove('active');
      if (sidebar) sidebar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    var sidebarLinks = document.querySelectorAll('#tripSidebar ul li a');
    sidebarLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 768) closeSidebar();
      });
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 768) closeSidebar();
    });

    var currentStep = 1, totalSteps = 14;
    var panels = document.querySelectorAll('.trip-step-panel');
    var stepLabels = document.querySelectorAll('#tripStepperSteps .trip-step');
    var btnPrev = document.querySelector('.btn-prev');
    var btnNext = document.querySelector('.btn-next');
    var secondStopsInitialized = false;
    var thirdStopsInitialized = false;
    var destAutocomplete2Inited = false;
    var destAutocomplete3Inited = false;
    function wantsSecondDestination() {
      return (document.getElementById('add_another_destination') || {}).value === 'yes';
    }
    function wantsThirdDestination() {
      return (document.getElementById('add_third_destination') || {}).value === 'yes';
    }
    function updateLegStepperVisibility() {
      var second = wantsSecondDestination();
      var third = wantsThirdDestination();
      document.querySelectorAll('.trip-step--second-leg').forEach(function (el) {
        if (second) {
          el.classList.remove('trip-step--second-leg-hidden');
          el.setAttribute('aria-hidden', 'false');
        } else {
          el.classList.add('trip-step--second-leg-hidden');
          el.setAttribute('aria-hidden', 'true');
        }
      });
      document.querySelectorAll('.trip-step--third-prompt').forEach(function (el) {
        if (second) {
          el.classList.remove('trip-step--third-prompt-hidden');
          el.setAttribute('aria-hidden', 'false');
        } else {
          el.classList.add('trip-step--third-prompt-hidden');
          el.setAttribute('aria-hidden', 'true');
        }
      });
      document.querySelectorAll('.trip-step--third-leg').forEach(function (el) {
        if (second && third) {
          el.classList.remove('trip-step--third-leg-hidden');
          el.setAttribute('aria-hidden', 'false');
        } else {
          el.classList.add('trip-step--third-leg-hidden');
          el.setAttribute('aria-hidden', 'true');
        }
      });
    }
    function computeNextStep(from) {
      if (from === 4 && !wantsSecondDestination()) return 10;
      if (from === 7 && !wantsThirdDestination()) return 10;
      if (from < totalSteps) return from + 1;
      return from;
    }
    function computePrevStep(from) {
      if (from === 10) {
        if (wantsThirdDestination()) return 9;
        if (wantsSecondDestination()) return 7;
        return 4;
      }
      if (from > 1) return from - 1;
      return from;
    }
    function escSummaryHtml(s) {
      var t = document.createElement('div');
      t.textContent = s == null ? '' : String(s);
      return t.innerHTML;
    }
    function fmtSummaryDate(iso) {
      if (!iso || !String(iso).trim()) return '—';
      var s = String(iso).trim();
      var d = new Date(s.indexOf('T') === -1 ? s + 'T12:00:00' : s);
      if (isNaN(d.getTime())) return s;
      return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }
    function parsePriceFromSummaryItem(rowEl) {
      var ps = rowEl.querySelectorAll('p');
      for (var i = 0; i < ps.length; i++) {
        var tx = ps[i].textContent || '';
        if (!/total price/i.test(tx)) continue;
        var rest = String(tx).replace(/^[\s\S]*?total price\s*:?\s*/i, '');
        rest = rest.replace(/,/g, '');
        rest = rest.replace(/Rs\.?/gi, '').replace(/LKR/gi, '').trim();
        var num = rest.replace(/[^0-9.]/g, '');
        if (!num) return 0;
        var v = parseFloat(num);
        if (isNaN(v)) return 0;
        return Math.round(v);
      }
      return 0;
    }
    function parseFareFromStop(card) {
      var fareH = card.querySelector('.trip-stop-fare-amount');
      var v = fareH && fareH.value ? parseFloat(String(fareH.value).replace(/[^0-9.]/g, '')) : NaN;
      return isNaN(v) ? 0 : v;
    }
    function collectWizardSnapshotForSubmit() {
      function collectStopsFromList(listId) {
        var out = [];
        var list = document.getElementById(listId);
        if (!list) return out;
        list.querySelectorAll('.trip-stop-card').forEach(function (card) {
          var loc = ((card.querySelector('.trip-stop-location') || {}).value || '').trim();
          var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
          var trNo = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="no"]');
          var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
          var gNo = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="no"]');
          var tYes = trYes && trYes.classList.contains('selected');
          var tNo = trNo && trNo.classList.contains('selected');
          var guYes = gYes && gYes.classList.contains('selected');
          var guNo = gNo && gNo.classList.contains('selected');
          var row = { location: loc, transport: null, guide: null };
          if (tYes) {
            var fareNum = parseFareFromStop(card);
            row.transport = {
              pickup: ((card.querySelector('.trip-stop-pickup') || {}).value || '').trim(),
              dropoff: ((card.querySelector('.trip-stop-dropoff') || {}).value || '').trim(),
              date: ((card.querySelector('.trip-stop-tr-date') || {}).value || '').trim(),
              vehicle: ((card.querySelector('.trip-stop-tr-vehicle') || {}).value || '').trim(),
              time: ((card.querySelector('.trip-stop-tr-time') || {}).value || '').trim(),
              people: ((card.querySelector('.trip-stop-tr-people') || {}).value || '').trim(),
              fare: fareNum
            };
          } else if (tNo) {
            row.transport = { notRequested: true };
          }
          if (guYes) {
            row.guide = {
              location: ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim(),
              date: ((card.querySelector('.trip-stop-guide-date') || {}).value || '').trim(),
              language: ((card.querySelector('.trip-stop-guide-language') || {}).value || '').trim(),
              time: ((card.querySelector('.trip-stop-guide-time') || {}).value || '').trim(),
              notes: ((card.querySelector('.trip-stop-guide-notes') || {}).value || '').trim()
            };
          } else if (guNo) {
            row.guide = { notRequested: true };
          }
          out.push(row);
        });
        return out;
      }
      function legBlock(num, destId, startId, endId, stopsListId) {
        var de = document.getElementById(destId);
        var se = document.getElementById(startId);
        var ee = document.getElementById(endId);
        return {
          leg: num,
          destination: de ? (de.value || '').trim() : '',
          start_date: se ? (se.value || '').trim() : '',
          end_date: ee ? (ee.value || '').trim() : '',
          stops: collectStopsFromList(stopsListId)
        };
      }
      var legs = [legBlock(1, 'dest_primary', 'start_date', 'end_date', 'tripStopsList')];
      if (wantsSecondDestination()) {
        legs.push(legBlock(2, 'dest_primary_2', 'start_date_2', 'end_date_2', 'tripStopsList_2'));
      }
      if (wantsThirdDestination()) {
        legs.push(legBlock(3, 'dest_primary_3', 'start_date_3', 'end_date_3', 'tripStopsList_3'));
      }
      return {
        trip_type: ((document.getElementById('trip_type') || {}).value || '').trim(),
        adults: parseInt((document.getElementById('adults') || {}).value, 10) || 0,
        children: parseInt((document.getElementById('children') || {}).value, 10) || 0,
        infants: parseInt((document.getElementById('infants') || {}).value, 10) || 0,
        legs: legs
      };
    }
    function computeTripBudgetTotals() {
      var transportSum = 0;
      var accSum = 0;
      var guideSum = 0;
      var TOUR_GUIDE_RATE_LKR = 2500;
      function tallyStops(stopsListEl) {
        if (!stopsListEl) return;
        stopsListEl.querySelectorAll('.trip-stop-card').forEach(function (card) {
          var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
          var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
          var tYes = trYes && trYes.classList.contains('selected');
          var guYes = gYes && gYes.classList.contains('selected');
          if (tYes) {
            var fareNum = parseFareFromStop(card);
            if (fareNum) transportSum += fareNum;
          }
          if (guYes) guideSum += TOUR_GUIDE_RATE_LKR;
        });
      }
      tallyStops(document.getElementById('tripStopsList'));
      if (wantsSecondDestination()) tallyStops(document.getElementById('tripStopsList_2'));
      if (wantsThirdDestination()) tallyStops(document.getElementById('tripStopsList_3'));
      function addAcc(bodyId) {
        var body = document.getElementById(bodyId);
        if (!body) return;
        body.querySelectorAll('.trip-accommodation-summary-item').forEach(function (item) {
          var price = parsePriceFromSummaryItem(item);
          if (price) accSum += price;
        });
      }
      addAcc('tripAccommodationSummaryBody');
      if (wantsSecondDestination()) addAcc('trip2AccommodationSummaryBody');
      if (wantsThirdDestination()) addAcc('trip3AccommodationSummaryBody');
      var defaultBudgetWhenNoExpenses = 5000;
      var grand = transportSum + accSum + guideSum;
      if (!grand || grand <= 0) grand = defaultBudgetWhenNoExpenses;
      return {
        transportSum: transportSum,
        accSum: accSum,
        guideSum: guideSum,
        grand: grand,
        TOUR_GUIDE_RATE_LKR: TOUR_GUIDE_RATE_LKR
      };
    }
    function renderTripSummaryBudget(mountOverride) {
      var mount = mountOverride;
      if (typeof mount === 'string') {
        mount = document.getElementById(mount);
      }
      if (!mount) {
        mount = document.getElementById('tripSummaryDetailedMount');
      }
      if (!mount) return;

      var hy = null;
      try {
        if (window.__tripBudgetHydrate) {
          hy = window.__tripBudgetHydrate;
          delete window.__tripBudgetHydrate;
        }
      } catch (eHy) {
        hy = null;
      }
      var tripRowHy = hy && hy.trip ? hy.trip : null;
      var snapHy = hy && hy.snapshot ? hy.snapshot : null;
      var ws = snapHy && snapHy.wizard_snapshot ? snapHy.wizard_snapshot : null;

      var tt = ((document.getElementById('trip_type') || {}).value || '').trim();
      var ttLabel = tt ? tt.charAt(0).toUpperCase() + tt.slice(1).toLowerCase() : '—';
      var a = parseInt((document.getElementById('adults') || {}).value, 10) || 0;
      var c = parseInt((document.getElementById('children') || {}).value, 10) || 0;
      var inf = parseInt((document.getElementById('infants') || {}).value, 10) || 0;

      var transportSum = 0;
      var accSum = 0;
      var guideSum = 0;
      var TOUR_GUIDE_RATE_LKR = 2500;

      function kvTable(rows) {
        var h = '<table class="trip-sum-kv-table"><tbody>';
        rows.forEach(function (r) {
          h += '<tr><th scope="row">' + escSummaryHtml(r[0]) + '</th><td>' + escSummaryHtml(r[1]) + '</td></tr>';
        });
        h += '</tbody></table>';
        return h;
      }

      function renderStopBlocks(stopsListEl, legLabel) {
        if (!stopsListEl) return '';
        var cards = stopsListEl.querySelectorAll('.trip-stop-card');
        var html = '';
        cards.forEach(function (card, si) {
          var n = si + 1;
          var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
          var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
          var tYes = trYes && trYes.classList.contains('selected');
          var guYes = gYes && gYes.classList.contains('selected');
          var loc = ((card.querySelector('.trip-stop-location') || {}).value || '').trim();
          var fareNum = parseFareFromStop(card);
          if (tYes && fareNum) transportSum += fareNum;
          var fareDisp = fareNum ? formatLkr(fareNum) : '—';

          html += '<div class="trip-sum-stop-wrap">';
          html += '<p class="trip-sum-stop-attr-title"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span class="trip-sum-stop-badge">Stop ' + n + (loc ? ': ' + escSummaryHtml(loc) : '') + '</span></p>';

          if (tYes) {
            var pu = ((card.querySelector('.trip-stop-pickup') || {}).value || '').trim();
            var dof = ((card.querySelector('.trip-stop-dropoff') || {}).value || '').trim();
            var td = ((card.querySelector('.trip-stop-tr-date') || {}).value || '').trim();
            var veh = ((card.querySelector('.trip-stop-tr-vehicle') || {}).value || '').trim();
            var tm = ((card.querySelector('.trip-stop-tr-time') || {}).value || '').trim();
            var np = ((card.querySelector('.trip-stop-tr-people') || {}).value || '').trim();
            html += '<div class="trip-sum-service-block">';
            html += '<div class="trip-sum-service-head trip-sum-service-head--transport"><span class="trip-sum-service-head-main"><i class="fa-solid fa-bus" aria-hidden="true"></i> Stop ' + n + ' — Transport</span><span class="trip-sum-service-head-amt">' + escSummaryHtml(fareDisp) + '</span></div>';
            html += '<div class="trip-sum-service-body">' + kvTable([
              ['Date', td ? fmtSummaryDate(td) : '—'],
              ['Vehicle type', veh || '—'],
              ['Pickup time', tm || '—'],
              ['Pickup location', pu || '—'],
              ['Dropoff location', dof || '—'],
              ['No. of people', np || '—'],
              ['Estimated fare (LKR)', fareNum ? String(Math.round(fareNum)) : '—']
            ]) + '</div></div>';
          }

          if (guYes) {
            var gloc = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
            var gdt = ((card.querySelector('.trip-stop-guide-date') || {}).value || '').trim();
            var glang = ((card.querySelector('.trip-stop-guide-language') || {}).value || '').trim();
            var gtime = ((card.querySelector('.trip-stop-guide-time') || {}).value || '').trim();
            var gnotes = ((card.querySelector('.trip-stop-guide-notes') || {}).value || '').trim();
            var gQty = 1;
            var gLine = TOUR_GUIDE_RATE_LKR * gQty;
            guideSum += gLine;
            html += '<div class="trip-sum-service-block">';
            html += '<div class="trip-sum-service-head trip-sum-service-head--guide trip-sum-service-head--guide-3amt">';
            html += '<span class="trip-sum-service-head-main"><i class="fa-solid fa-user-tie" aria-hidden="true"></i> Stop ' + n + ' — Tour guide</span>';
            html += '<div class="trip-sum-service-head-amt-grid" role="group" aria-label="Tour guide amounts (LKR)">';
            html += '<div class="trip-sum-amt-col"><span class="trip-sum-amt-hd">Unit rate</span><span class="trip-sum-amt-val">' + escSummaryHtml(formatLkr(TOUR_GUIDE_RATE_LKR)) + '</span></div>';
            html += '<div class="trip-sum-amt-col"><span class="trip-sum-amt-hd">Qty</span><span class="trip-sum-amt-val">' + escSummaryHtml(String(gQty)) + '</span></div>';
            html += '<div class="trip-sum-amt-col"><span class="trip-sum-amt-hd">Subtotal</span><span class="trip-sum-amt-val">' + escSummaryHtml(formatLkr(gLine)) + '</span></div>';
            html += '</div></div>';
            html += '<div class="trip-sum-service-body">' + kvTable([
              ['Location / attraction', gloc || '—'],
              ['Preferred date', gdt ? fmtSummaryDate(gdt) : '—'],
              ['Language', glang || '—'],
              ['Preferred time', gtime || '—'],
              ['Notes', gnotes || '—']
            ]) + '</div></div>';
          }
          html += '</div>';
        });
        return html;
      }

      function renderStopBlocksFromSnapshot(stopsArr) {
        if (!Array.isArray(stopsArr) || stopsArr.length === 0) return '';
        var html = '';
        stopsArr.forEach(function (item, si) {
          var n = si + 1;
          var loc = (item && item.location) ? String(item.location).trim() : '';
          var tr = item && item.transport ? item.transport : null;
          var gu = item && item.guide ? item.guide : null;
          var tYes = tr && tr.notRequested !== true && tr.fare != null && parseFloat(tr.fare) > 0;
          if (!tYes && tr && !tr.notRequested && tr.pickup) tYes = true;
          var fareNum = tr && tr.fare != null ? parseFloat(tr.fare) : 0;
          if (isNaN(fareNum)) fareNum = 0;
          if (tYes && fareNum) transportSum += fareNum;
          var fareDisp = fareNum ? formatLkr(fareNum) : '—';

          html += '<div class="trip-sum-stop-wrap">';
          html += '<p class="trip-sum-stop-attr-title"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span class="trip-sum-stop-badge">Stop ' + n + (loc ? ': ' + escSummaryHtml(loc) : '') + '</span></p>';

          if (tYes && tr) {
            var pu = (tr.pickup || '').trim();
            var dof = (tr.dropoff || '').trim();
            var td = (tr.date || '').trim();
            var veh = (tr.vehicle || '').trim();
            var tm = (tr.time || '').trim();
            var np = (tr.people || '').trim();
            html += '<div class="trip-sum-service-block">';
            html += '<div class="trip-sum-service-head trip-sum-service-head--transport"><span class="trip-sum-service-head-main"><i class="fa-solid fa-bus" aria-hidden="true"></i> Stop ' + n + ' — Transport</span><span class="trip-sum-service-head-amt">' + escSummaryHtml(fareDisp) + '</span></div>';
            html += '<div class="trip-sum-service-body">' + kvTable([
              ['Date', td ? fmtSummaryDate(td) : '—'],
              ['Vehicle type', veh || '—'],
              ['Pickup time', tm || '—'],
              ['Pickup location', pu || '—'],
              ['Dropoff location', dof || '—'],
              ['No. of people', np || '—'],
              ['Estimated fare (LKR)', fareNum ? String(Math.round(fareNum)) : '—']
            ]) + '</div></div>';
          }

          var guYes = gu && gu.notRequested !== true && gu.location;
          if (guYes) {
            var gloc = (gu.location || '').trim();
            var gdt = (gu.date || '').trim();
            var glang = (gu.language || '').trim();
            var gtime = (gu.time || '').trim();
            var gnotes = (gu.notes || '').trim();
            var gQty = 1;
            var gLine = TOUR_GUIDE_RATE_LKR * gQty;
            guideSum += gLine;
            html += '<div class="trip-sum-service-block">';
            html += '<div class="trip-sum-service-head trip-sum-service-head--guide trip-sum-service-head--guide-3amt">';
            html += '<span class="trip-sum-service-head-main"><i class="fa-solid fa-user-tie" aria-hidden="true"></i> Stop ' + n + ' — Tour guide</span>';
            html += '<div class="trip-sum-service-head-amt-grid" role="group" aria-label="Tour guide amounts (LKR)">';
            html += '<div class="trip-sum-amt-col"><span class="trip-sum-amt-hd">Unit rate</span><span class="trip-sum-amt-val">' + escSummaryHtml(formatLkr(TOUR_GUIDE_RATE_LKR)) + '</span></div>';
            html += '<div class="trip-sum-amt-col"><span class="trip-sum-amt-hd">Qty</span><span class="trip-sum-amt-val">' + escSummaryHtml(String(gQty)) + '</span></div>';
            html += '<div class="trip-sum-amt-col"><span class="trip-sum-amt-hd">Subtotal</span><span class="trip-sum-amt-val">' + escSummaryHtml(formatLkr(gLine)) + '</span></div>';
            html += '</div></div>';
            html += '<div class="trip-sum-service-body">' + kvTable([
              ['Location / attraction', gloc || '—'],
              ['Preferred date', gdt ? fmtSummaryDate(gdt) : '—'],
              ['Language', glang || '—'],
              ['Preferred time', gtime || '—'],
              ['Notes', gnotes || '—']
            ]) + '</div></div>';
          }
          html += '</div>';
        });
        return html;
      }

      function formatLkr(n) {
        if (!n || n <= 0) return '—';
        return 'LKR ' + Math.round(n).toLocaleString('en-LK');
      }

      function renderAccBlock(title, bodyId) {
        var accThumb = '<div class="trip-sum-thumb trip-sum-thumb--acc" role="presentation"><img src="/CeylonGo/public/images/5star.jpg" alt="" loading="lazy" width="200" height="120"></div>';
        var body = document.getElementById(bodyId);
        if (!body || !body.querySelector('.trip-accommodation-summary-item')) {
          return '';
        }
        var items = body.querySelectorAll('.trip-accommodation-summary-item');
        var blockHtml = '';
        items.forEach(function (item, ix) {
          var price = parsePriceFromSummaryItem(item);
          if (price) accSum += price;
          var ps = item.querySelectorAll('p');
          var hotel = '';
          var cin = '';
          var cout = '';
          var tp = '';
          ps.forEach(function (p) {
            var tx = p.textContent || '';
            if (/^Hotel:/i.test(tx)) hotel = tx.replace(/^Hotel:\s*/i, '').trim();
            if (/^Check-in:/i.test(tx)) cin = tx.replace(/^Check-in:\s*/i, '').trim();
            if (/^Check-out:/i.test(tx)) cout = tx.replace(/^Check-out:\s*/i, '').trim();
            if (/^Total price:/i.test(tx)) tp = tx.replace(/^Total price:\s*/i, '').trim();
          });
          var amt = price ? formatLkr(price) : (tp || '—');
          var headTitle = title + (items.length > 1 ? ' — Booking ' + (ix + 1) : '');
          blockHtml += '<div class="trip-sum-service-block">';
          blockHtml += '<div class="trip-sum-service-head trip-sum-service-head--hotel"><span class="trip-sum-service-head-main"><i class="fa-solid fa-hotel" aria-hidden="true"></i> ' + escSummaryHtml(headTitle) + '</span><span class="trip-sum-service-head-amt">' + escSummaryHtml(amt) + '</span></div>';
          blockHtml += '<div class="trip-sum-service-body">' + accThumb + kvTable([
            ['Hotel', hotel || '—'],
            ['Check-in', cin || '—'],
            ['Check-out', cout || '—'],
            ['Room / total', tp || '—']
          ]) + '</div></div>';
        });
        return '<div class="trip-sum-accommodation-section">' + blockHtml + '</div>';
      }

      function destSection(legNum, destName, startEl, endEl, durEl, stopsId, hotelTitle, accBodyId, snapshotStopsOpt) {
        var sd = startEl && startEl.value ? fmtSummaryDate(startEl.value) : '—';
        var ed = endEl && endEl.value ? fmtSummaryDate(endEl.value) : '—';
        var dur = durEl ? (durEl.textContent || '').trim() : '—';
        var locLine = destName || '—';
        var stopsEl = document.getElementById(stopsId);
        var html = '';
        html += '<section class="trip-sum-destination-section" data-destination="' + legNum + '">';
        html += '<div class="trip-sum-destination-shell">';
        html += '<h3 class="trip-sum-dest-main-title">Destination ' + legNum + ' — ' + escSummaryHtml(locLine) + '</h3>';
        html += '<div class="trip-sum-info-panel">';
        html += '<h4 class="trip-sum-info-panel-title">Destination ' + legNum + ' info</h4>';
        html += '<div class="trip-sum-info-panel-inner">';
        html += '<div class="trip-sum-info-table-wrap">' + kvTable([
          ['Location', locLine],
          ['Travel dates', sd + ' — ' + ed],
          ['Duration', dur && dur !== '—' ? dur : '—']
        ]) + '</div>';
        html += '</div></div>';
        html += '<div class="trip-sum-destination-narrow">';
        if (Array.isArray(snapshotStopsOpt)) {
          if (snapshotStopsOpt.length > 0) {
            html += renderStopBlocksFromSnapshot(snapshotStopsOpt);
          } else if (locLine && locLine !== '—') {
            html += renderStopBlocksFromSnapshot([{ location: locLine, transport: null, guide: null }]);
          }
        } else {
          var hasCards = stopsEl && stopsEl.querySelectorAll('.trip-stop-card').length > 0;
          if (hasCards) {
            html += renderStopBlocks(stopsEl, legNum);
          } else if (locLine && locLine !== '—') {
            html += renderStopBlocksFromSnapshot([{ location: locLine, transport: null, guide: null }]);
          } else {
            html += renderStopBlocks(stopsEl, legNum);
          }
        }
        html += renderAccBlock(hotelTitle, accBodyId);
        html += '</div>';
        html += '</div>';
        html += '</section>';
        return html;
      }

      if (ws && typeof ws === 'object') {
        if (ws.trip_type) {
          tt = String(ws.trip_type).trim();
          ttLabel = tt ? tt.charAt(0).toUpperCase() + tt.slice(1).toLowerCase() : '—';
        }
        if (ws.adults != null) a = parseInt(ws.adults, 10) || 0;
        if (ws.children != null) c = parseInt(ws.children, 10) || 0;
        if (ws.infants != null) inf = parseInt(ws.infants, 10) || 0;
      }

      function computeDurFromDateStrings(sdVal, edVal) {
        if (!sdVal || !edVal) return '—';
        var d0 = new Date(String(sdVal).trim() + 'T12:00:00');
        var d1 = new Date(String(edVal).trim() + 'T12:00:00');
        if (isNaN(d0.getTime()) || isNaN(d1.getTime()) || d1 < d0) return '—';
        var nights = Math.round((d1 - d0) / 86400000);
        if (nights === 0) return '0 Nights';
        return nights + ' Night' + (nights !== 1 ? 's' : '');
      }

      var mainHtml = '';
      mainHtml += '<div class="trip-summary-detailed-layout">';
      mainHtml += '<div class="trip-summary-detailed-main">';

      mainHtml += '<div class="trip-sum-party-banner">';
      mainHtml += '<div class="trip-sum-party-contact">';
      mainHtml += '<div class="trip-sum-party-contact-line"><span class="trip-sum-party-contact-label">Name</span> ' + escSummaryHtml(tripUserName || '—') + '</div>';
      mainHtml += '<div class="trip-sum-party-contact-line"><span class="trip-sum-party-contact-label">Email</span> ' + escSummaryHtml(tripUserEmail || '—') + '</div>';
      mainHtml += '<div class="trip-sum-party-contact-line"><span class="trip-sum-party-contact-label">Contact</span> ' + escSummaryHtml(tripUserContact || '—') + '</div>';
      mainHtml += '</div>';
      mainHtml += '<div class="trip-sum-party-line"><strong>Travel party:</strong> ' + escSummaryHtml(ttLabel) + ' · ';
      mainHtml += escSummaryHtml(String(a)) + ' adult(s)';
      if (c) mainHtml += ', ' + escSummaryHtml(String(c)) + ' child(ren)';
      if (inf) mainHtml += ', ' + escSummaryHtml(String(inf)) + ' infant(s)';
      mainHtml += '</div></div>';

      if (ws && Array.isArray(ws.legs) && ws.legs.length > 0) {
        ws.legs.forEach(function (leg, idx) {
          var ln = leg.leg != null ? (parseInt(leg.leg, 10) || (idx + 1)) : (idx + 1);
          var sdV = (leg.start_date || '').trim();
          var edV = (leg.end_date || '').trim();
          var destNm = (leg.destination || '').trim();
          if (!destNm && ln === 1 && tripRowHy && tripRowHy.destination) destNm = String(tripRowHy.destination).trim();
          if (!sdV && ln === 1 && tripRowHy && tripRowHy.start_date) sdV = String(tripRowHy.start_date).trim();
          if (!sdV && ln === 1 && snapHy && snapHy.start_date) sdV = String(snapHy.start_date).trim();
          if (!edV && ln === 1 && snapHy && snapHy.end_date) edV = String(snapHy.end_date).trim();
          var fs = { value: sdV };
          var fe = { value: edV };
          var fdur = { textContent: computeDurFromDateStrings(sdV, edV) };
          var stopsArr = Array.isArray(leg.stops) ? leg.stops : [];
          var sid = ln === 1 ? 'tripStopsList' : (ln === 2 ? 'tripStopsList_2' : 'tripStopsList_3');
          var accT = ln === 1 ? 'Accommodation (destination 1)' : (ln === 2 ? 'Accommodation (destination 2)' : 'Accommodation (destination 3)');
          var accB = ln === 1 ? 'tripAccommodationSummaryBody' : (ln === 2 ? 'trip2AccommodationSummaryBody' : 'trip3AccommodationSummaryBody');
          mainHtml += destSection(ln, destNm, fs, fe, fdur, sid, accT, accB, stopsArr);
        });
      } else {
        var d1 = ((document.getElementById('dest_primary') || {}).value || '').trim();
        if (!d1 && tripRowHy) d1 = String(tripRowHy.destination || '').trim();
        if (!d1 && snapHy && snapHy.destination) d1 = String(snapHy.destination || '').trim();

        var startEl = document.getElementById('start_date');
        var endEl = document.getElementById('end_date');
        var sdVal = (startEl && startEl.value) ? String(startEl.value).trim() : '';
        if (!sdVal && tripRowHy && tripRowHy.start_date) sdVal = String(tripRowHy.start_date).trim();
        if (!sdVal && snapHy && snapHy.start_date) sdVal = String(snapHy.start_date).trim();
        var edVal = (endEl && endEl.value) ? String(endEl.value).trim() : '';
        if (!edVal && snapHy && snapHy.end_date) edVal = String(snapHy.end_date).trim();

        var fakeStart = sdVal ? { value: sdVal } : startEl;
        var fakeEnd = edVal ? { value: edVal } : endEl;
        var durElUse = (sdVal && edVal) ? { textContent: computeDurFromDateStrings(sdVal, edVal) } : document.getElementById('tripDuration');

        mainHtml += destSection(1, d1, fakeStart, fakeEnd, durElUse, 'tripStopsList', 'Accommodation (destination 1)', 'tripAccommodationSummaryBody');

        if (wantsSecondDestination()) {
          var d2 = ((document.getElementById('dest_primary_2') || {}).value || '').trim();
          mainHtml += destSection(2, d2,
            document.getElementById('start_date_2'), document.getElementById('end_date_2'),
            document.getElementById('tripDuration_2'), 'tripStopsList_2',
            'Accommodation (destination 2)', 'trip2AccommodationSummaryBody');
        }

        if (wantsThirdDestination()) {
          var d3 = ((document.getElementById('dest_primary_3') || {}).value || '').trim();
          mainHtml += destSection(3, d3,
            document.getElementById('start_date_3'), document.getElementById('end_date_3'),
            document.getElementById('tripDuration_3'), 'tripStopsList_3',
            'Accommodation (destination 3)', 'trip3AccommodationSummaryBody');
        }
      }

      mainHtml += '</div>';

      mainHtml += '<aside class="trip-summary-budget-aside">';
      mainHtml += '<div class="trip-sum-budget-card">';
      mainHtml += '<div class="trip-sum-budget-card-head">Budget summary</div>';
      mainHtml += '<ul class="trip-sum-budget-lines">';
      mainHtml += '<li><span>Transport total</span><strong id="tripBudgetLineTransport">' + escSummaryHtml(formatLkr(transportSum)) + '</strong></li>';
      mainHtml += '<li><span>Tour guide total</span><strong id="tripBudgetLineGuide">' + escSummaryHtml(formatLkr(guideSum)) + '</strong></li>';
      mainHtml += '<li><span>Accommodation total</span><strong id="tripBudgetLineAcc">' + escSummaryHtml(formatLkr(accSum)) + '</strong></li>';
      mainHtml += '<li><span>Other expenses</span><strong>—</strong></li>';
      mainHtml += '</ul>';
      var defaultBudgetWhenNoExpenses = 5000;
      var grand = transportSum + accSum + guideSum;
      if ((!grand || grand <= 0) && tripRowHy) {
        var blHy = parseFloat(tripRowHy.budget_lkr);
        if (!isNaN(blHy) && blHy > 0) grand = blHy;
      }
      if ((!grand || grand <= 0) && snapHy) {
        var bsHy = parseFloat(snapHy.budget_lkr);
        if (!isNaN(bsHy) && bsHy > 0) grand = bsHy;
      }
      if (!grand || grand <= 0) grand = defaultBudgetWhenNoExpenses;
      mainHtml += '<div class="trip-sum-budget-total"><span>Total budget</span><strong id="tripBudgetLineGrand">' + escSummaryHtml(formatLkr(grand)) + '</strong></div>';
      mainHtml += '<p class="trip-sum-budget-footnote">Transport fares use estimates from the calculator. Hotel totals use saved bookings. Each requested tour guide is LKR ' + escSummaryHtml(String(TOUR_GUIDE_RATE_LKR.toLocaleString('en-LK'))) + ' per stop.</p>';
      mainHtml += '</div></aside>';

      mainHtml += '</div>';

      mount.innerHTML = mainHtml;
    }

    function renderTripSummaryItinerary() {
      var mount = document.getElementById('tripItinerarySummaryMount');
      if (!mount) return;

      var tt = ((document.getElementById('trip_type') || {}).value || '').trim();
      var ttLabel = tt ? tt.charAt(0).toUpperCase() + tt.slice(1).toLowerCase() : '—';
      var a = parseInt((document.getElementById('adults') || {}).value, 10) || 0;
      var c = parseInt((document.getElementById('children') || {}).value, 10) || 0;
      var inf = parseInt((document.getElementById('infants') || {}).value, 10) || 0;

      var TOUR_GUIDE_RATE_LKR = 2500;

      function formatLkr(n) {
        if (!n || n <= 0) return '—';
        return 'LKR ' + Math.round(n).toLocaleString('en-LK');
      }

      function pad2(n) {
        return n < 10 ? '0' + n : String(n);
      }
      function parseLocalDateFromInput(iso) {
        if (!iso || !String(iso).trim()) return null;
        var parts = String(iso).trim().split('-');
        if (parts.length < 3) return null;
        var y = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10) - 1;
        var d = parseInt(parts[2], 10);
        if (isNaN(y) || isNaN(m) || isNaN(d)) return null;
        return new Date(y, m, d, 12, 0, 0);
      }
      function dateKeyFromDate(d) {
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
      }
      function dateKeyFromIso(s) {
        if (!s || !String(s).trim()) return '';
        var t = String(s).trim();
        return t.length >= 10 ? t.slice(0, 10) : t;
      }
      function eachCalendarDayInclusive(startIso, endIso) {
        var a = parseLocalDateFromInput(startIso);
        var b = parseLocalDateFromInput(endIso);
        if (!a || !b || b < a) return [];
        var out = [];
        var cur = new Date(a.getTime());
        var endT = b.getTime();
        while (cur.getTime() <= endT) {
          out.push(new Date(cur.getTime()));
          cur.setDate(cur.getDate() + 1);
        }
        return out;
      }
      function fmtSummaryDateCompact(d) {
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      }

      function buildCardTitle(dayDate, leg, legIdx, isFirst, isLast, totalLegs, legConfigs) {
        var compact = fmtSummaryDateCompact(dayDate);
        var dest = (leg.destName || '').trim() || 'your destination';
        var prev = legIdx > 0 ? ((legConfigs[legIdx - 1].destName || '').trim()) : '';
        var next = legIdx < legConfigs.length - 1 ? ((legConfigs[legIdx + 1].destName || '').trim()) : '';
        if (isFirst && isLast) {
          return compact + ': ' + dest;
        }
        if (legIdx === 0 && isFirst) {
          return compact + ': Arrival — ' + dest;
        }
        if (legIdx > 0 && isFirst && prev) {
          return compact + ': ' + prev + ' to ' + dest;
        }
        if (legIdx > 0 && isFirst) {
          return compact + ': Arrival — ' + dest;
        }
        if (isLast && legIdx < totalLegs - 1 && next) {
          return compact + ': ' + dest + ' — onward to ' + next;
        }
        if (isLast && legIdx === totalLegs - 1) {
          return compact + ': Departure — ' + dest;
        }
        if (!isFirst && !isLast) {
          return compact + ': Full day — ' + dest;
        }
        return compact + ': ' + dest;
      }

      function parseFirstAccLines(bodyId) {
        var body = document.getElementById(bodyId);
        if (!body) return null;
        var first = body.querySelector('.trip-accommodation-summary-item');
        if (!first) return null;
        var ps = first.querySelectorAll('p');
        var hotel = '';
        var cin = '';
        var cout = '';
        var tp = '';
        ps.forEach(function (p) {
          var tx = p.textContent || '';
          if (/^Hotel:/i.test(tx)) hotel = tx.replace(/^Hotel:\s*/i, '').trim();
          if (/^Check-in:/i.test(tx)) cin = tx.replace(/^Check-in:\s*/i, '').trim();
          if (/^Check-out:/i.test(tx)) cout = tx.replace(/^Check-out:\s*/i, '').trim();
          if (/^Total price:/i.test(tx)) tp = tx.replace(/^Total price:\s*/i, '').trim();
        });
        return { hotel: hotel, cin: cin, cout: cout, tp: tp };
      }

      function buildDayBullets(dayDate, leg, stopsEl, isFirst, isLast, totalLegs) {
        var dk = dateKeyFromDate(dayDate);
        var destName = (leg.destName || '').trim() || '—';
        var bullets = [];

        if (isFirst && isLast) {
          bullets.push('Your stay in ' + destName + ' — ' + fmtSummaryDate(leg.startEl && leg.startEl.value) + ' to ' + fmtSummaryDate(leg.endEl && leg.endEl.value) + '.');
        } else if (isFirst) {
          bullets.push('Arrival and start of your time in ' + destName + '.');
        } else if (isLast) {
          if (leg.num < totalLegs) {
            bullets.push('Last day in ' + destName + ' before your next destination.');
          } else {
            bullets.push('Final day in ' + destName + '.');
          }
        } else {
          bullets.push('Explore ' + destName + ' — sightseeing, food, and local experiences.');
        }

        if (isFirst && stopsEl) {
          stopsEl.querySelectorAll('.trip-stop-card').forEach(function (card, si) {
            var loc = ((card.querySelector('.trip-stop-location') || {}).value || '').trim();
            if (loc) bullets.push('Planned stop ' + (si + 1) + ': ' + loc);
          });
        }

        if (stopsEl) {
          var stopCards = stopsEl.querySelectorAll('.trip-stop-card');
          stopCards.forEach(function (card, si) {
            var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
            var tYes = trYes && trYes.classList.contains('selected');
            if (!tYes) return;
            // Transport details are intentionally omitted from the itinerary bullets.
          });
        }

        if (stopsEl) {
          var stopCardsG = stopsEl.querySelectorAll('.trip-stop-card');
          stopCardsG.forEach(function (card, si) {
            var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
            var guYes = gYes && gYes.classList.contains('selected');
            if (!guYes) return;
            var gdt = dateKeyFromIso(((card.querySelector('.trip-stop-guide-date') || {}).value || '').trim());
            var gloc = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
            var glang = ((card.querySelector('.trip-stop-guide-language') || {}).value || '').trim();
            var onDay = gdt ? (gdt === dk) : isFirst;
            if (!onDay) return;
            var gline = 'Tour guide' + (stopCardsG.length > 1 ? ' (stop ' + (si + 1) + ')' : '') + ': ' + (gloc || '—');
            if (glang) gline += ' · ' + glang;
            gline += ' · ' + formatLkr(TOUR_GUIDE_RATE_LKR);
            bullets.push(gline);
          });
        }

        if (isFirst) {
          var acc = parseFirstAccLines(leg.accBodyId);
          if (acc && acc.hotel) {
            var hline = 'Hotel: ' + acc.hotel;
            if (acc.cin || acc.cout) hline += ' · ' + [acc.cin, acc.cout].filter(Boolean).join(' → ');
            if (acc.tp) hline += ' · ' + acc.tp;
            bullets.push(hline);
          }
        }

        return bullets;
      }

      function buildDayCardHtml(dayDate, leg, legIdx, stopsEl, isFirst, isLast, totalLegs, legConfigs, tripDayNum) {
        var title = buildCardTitle(dayDate, leg, legIdx, isFirst, isLast, totalLegs, legConfigs);
        var badge = String(tripDayNum);
        var bullets = buildDayBullets(dayDate, leg, stopsEl, isFirst, isLast, totalLegs);
        var listHtml = bullets.map(function (b) {
          return '<li>' + escSummaryHtml(b) + '</li>';
        }).join('');
        return (
          '<article class="trip-sum-day-card">' +
          '<header class="trip-sum-day-card__header">' +
          '<span class="trip-sum-day-badge" aria-hidden="true">' + escSummaryHtml(badge) + '</span>' +
          '<h3 class="trip-sum-day-card__title">' + escSummaryHtml(title) + '</h3>' +
          '</header>' +
          '<ul class="trip-sum-day-card__list">' + listHtml + '</ul>' +
          '</article>'
        );
      }

      var legConfigs = [];
      var d1 = ((document.getElementById('dest_primary') || {}).value || '').trim();
      legConfigs.push({
        num: 1,
        destName: d1,
        startEl: document.getElementById('start_date'),
        endEl: document.getElementById('end_date'),
        stopsId: 'tripStopsList',
        accBodyId: 'tripAccommodationSummaryBody'
      });
      if (wantsSecondDestination()) {
        var d2 = ((document.getElementById('dest_primary_2') || {}).value || '').trim();
        legConfigs.push({
          num: 2,
          destName: d2,
          startEl: document.getElementById('start_date_2'),
          endEl: document.getElementById('end_date_2'),
          stopsId: 'tripStopsList_2',
          accBodyId: 'trip2AccommodationSummaryBody'
        });
      }
      if (wantsThirdDestination()) {
        var d3 = ((document.getElementById('dest_primary_3') || {}).value || '').trim();
        legConfigs.push({
          num: 3,
          destName: d3,
          startEl: document.getElementById('start_date_3'),
          endEl: document.getElementById('end_date_3'),
          stopsId: 'tripStopsList_3',
          accBodyId: 'trip3AccommodationSummaryBody'
        });
      }
      var totalLegs = legConfigs.length;

      var tripDayNum = 0;
      var timelineHtml = '<div class="trip-sum-timeline">';
      legConfigs.forEach(function (leg, legIdx) {
        var startV = leg.startEl && leg.startEl.value;
        var endV = leg.endEl && leg.endEl.value;
        var days = eachCalendarDayInclusive(startV, endV);
        var stopsEl = document.getElementById(leg.stopsId);
        if (!days.length) {
          tripDayNum += 1;
          timelineHtml += (
            '<article class="trip-sum-day-card trip-sum-day-card--placeholder">' +
            '<header class="trip-sum-day-card__header">' +
            '<span class="trip-sum-day-badge trip-sum-day-badge--muted" aria-hidden="true">' + escSummaryHtml(String(tripDayNum)) + '</span>' +
            '<h3 class="trip-sum-day-card__title">' + escSummaryHtml((leg.destName || 'This destination').trim() || 'Destination') + '</h3>' +
            '</header>' +
            '<ul class="trip-sum-day-card__list"><li>' + escSummaryHtml('Add start and end dates for this stay to see a day-by-day itinerary.') + '</li></ul>' +
            '</article>'
          );
          return;
        }
        days.forEach(function (d, di) {
          tripDayNum += 1;
          var isFirst = di === 0;
          var isLast = di === days.length - 1;
          timelineHtml += buildDayCardHtml(d, leg, legIdx, stopsEl, isFirst, isLast, totalLegs, legConfigs, tripDayNum);
        });
      });
      timelineHtml += '</div>';

      function mapBookingStatus(raw) {
        var s = (raw || '').toString().trim().toLowerCase();
        if (!s) return { text: '—', cls: 'trip-sum-status--muted' };
        if (s === 'pending') return { text: 'Pending', cls: 'trip-sum-status--pending' };
        if (s === 'confirmed' || s === 'completed' || s === 'approved' || s === 'accepted') {
          return { text: 'Accepted', cls: 'trip-sum-status--accepted' };
        }
        if (s === 'cancelled' || s === 'canceled') return { text: 'Cancelled', cls: 'trip-sum-status--cancelled' };
        return { text: String(raw), cls: 'trip-sum-status--muted' };
      }

      function bookingStatusCardHtml() {
        var rows = [];
        var pendingCount = 0;
        var acceptedCount = 0;
        var cancelledCount = 0;
        function bump(st) {
          var t = (st || '').toString().trim().toLowerCase();
          if (t === 'pending') pendingCount++;
          else if (t === 'confirmed' || t === 'completed' || t === 'approved' || t === 'accepted') acceptedCount++;
          else if (t === 'cancelled' || t === 'canceled') cancelledCount++;
        }
        var globalStopIdx = 0;
        legConfigs.forEach(function (leg) {
          var stopsEl = document.getElementById(leg.stopsId);
          var cards = stopsEl ? stopsEl.querySelectorAll('.trip-stop-card') : [];
          cards.forEach(function (card) {
            globalStopIdx++;
            var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
            var tOn = trYes && trYes.classList.contains('selected');
            if (tOn) {
              var ts = (card.getAttribute('data-transport-booking-status') || '').trim();
              var disp = ts ? mapBookingStatus(ts) : { text: 'Not requested', cls: 'trip-sum-status--muted' };
              if (ts) bump(ts);
              rows.push({ label: 'Stop ' + globalStopIdx + ' — Transport provider', disp: disp });
            }
            var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
            var gOn = gYes && gYes.classList.contains('selected');
            if (gOn) {
              var gs = (card.getAttribute('data-guide-booking-status') || '').trim();
              var dispG = gs ? mapBookingStatus(gs) : { text: 'Not requested', cls: 'trip-sum-status--muted' };
              if (gs) bump(gs);
              rows.push({ label: 'Stop ' + globalStopIdx + ' — Tour guide', disp: dispG });
            }
          });
          var accBody = document.getElementById(leg.accBodyId);
          var items = accBody ? accBody.querySelectorAll('.trip-accommodation-summary-item') : [];
          if (!items.length) {
            rows.push({
              label: 'Destination ' + leg.num + ' — Hotel',
              disp: { text: 'Not booked', cls: 'trip-sum-status--muted' }
            });
          } else {
            items.forEach(function (item) {
              var ps = item.querySelectorAll('p');
              var hn = '';
              for (var pi = 0; pi < ps.length; pi++) {
                var tx = (ps[pi].textContent || '');
                if (/^Hotel:/i.test(tx)) {
                  hn = tx.replace(/^Hotel:\s*/i, '').trim();
                  break;
                }
              }
              var bid = item.getAttribute('data-booking-id');
              var bst = (item.getAttribute('data-booking-status') || '').trim();
              var lab = 'Destination ' + leg.num + ' — ' + (hn ? hn : 'Hotel');
              var dispH;
              if (bid) {
                dispH = mapBookingStatus(bst || 'pending');
                bump(bst || 'pending');
              } else {
                dispH = { text: 'Not booked', cls: 'trip-sum-status--muted' };
              }
              rows.push({ label: lab, disp: dispH });
            });
          }
        });
        var linesHtml = rows.map(function (r) {
          return '<li><span>' + escSummaryHtml(r.label) + '</span><strong class="trip-sum-status ' + r.disp.cls + '">' + escSummaryHtml(r.disp.text) + '</strong></li>';
        }).join('');
        var summaryText = 'No confirmations yet';
        if (pendingCount || acceptedCount || cancelledCount) {
          var parts = [];
          if (pendingCount) parts.push(pendingCount + ' pending');
          if (acceptedCount) parts.push(acceptedCount + ' accepted');
          if (cancelledCount) parts.push(cancelledCount + ' cancelled');
          summaryText = parts.join(' · ');
        }
        return (
          '<div class="trip-sum-budget-card trip-sum-booking-status-card" role="region" aria-label="Status of bookings">' +
          '<div class="trip-sum-budget-card-head">Status of Bookings</div>' +
          '<ul class="trip-sum-budget-lines">' + linesHtml + '</ul>' +
          '<div class="trip-sum-budget-total trip-sum-booking-status-total"><span>Summary</span><strong class="trip-sum-booking-status-total-value">' + escSummaryHtml(summaryText) + '</strong></div>' +
          '<p class="trip-sum-budget-footnote">Please carefully review your trip before submitting. After submission, you won’t be able to modify it. If you need changes, please contact our team.</p>' +
          '<div class="trip-sum-booking-submit-wrap" id="tripWizardSubmitWrap">' +
            '<button type="button" class="trip-sum-booking-submit-btn" id="tripWizardSubmitBtn">Submit trip</button>' +
            '<p class="trip-sum-booking-submit-success" id="tripWizardSubmitSuccess" hidden>Your trip was submitted successfully.</p>' +
          '</div>' +
          '</div>'
        );
      }

      var bud = computeTripBudgetTotals();

      var mainHtml = '';
      mainHtml += '<div class="trip-summary-detailed-layout trip-summary-itinerary-layout">';
      mainHtml += '<div class="trip-summary-detailed-main">';
      mainHtml += '<div class="trip-sum-party-banner">';
      mainHtml += '<div class="trip-sum-party-budget-top" role="region" aria-label="Trip budget summary">';
      mainHtml += '<span class="trip-sum-party-budget-label">Total budget</span>';
      mainHtml += '<strong class="trip-sum-party-budget-value">' + escSummaryHtml(formatLkr(bud.grand)) + '</strong>';
      mainHtml += '</div>';
      mainHtml += '<div class="trip-sum-party-contact">';
      mainHtml += '<div class="trip-sum-party-contact-line"><span class="trip-sum-party-contact-label">Name</span> ' + escSummaryHtml(tripUserName || '—') + '</div>';
      mainHtml += '<div class="trip-sum-party-contact-line"><span class="trip-sum-party-contact-label">Email</span> ' + escSummaryHtml(tripUserEmail || '—') + '</div>';
      mainHtml += '<div class="trip-sum-party-contact-line"><span class="trip-sum-party-contact-label">Contact</span> ' + escSummaryHtml(tripUserContact || '—') + '</div>';
      mainHtml += '</div>';
      mainHtml += '<div class="trip-sum-party-line"><strong>Travel party:</strong> ' + escSummaryHtml(ttLabel) + ' · ';
      mainHtml += escSummaryHtml(String(a)) + ' adult(s)';
      if (c) mainHtml += ', ' + escSummaryHtml(String(c)) + ' child(ren)';
      if (inf) mainHtml += ', ' + escSummaryHtml(String(inf)) + ' infant(s)';
      mainHtml += '</div></div>';
      mainHtml += '<h3 class="trip-sum-itinerary-heading">Your itinerary</h3>';
      mainHtml += timelineHtml;
      mainHtml += '</div>';
      mainHtml += '<aside class="trip-summary-budget-aside trip-summary-booking-aside" aria-label="Status of bookings">';
      mainHtml += bookingStatusCardHtml();
      mainHtml += '</aside>';
      mainHtml += '</div>';

      mount.innerHTML = mainHtml;
      bindTripWizardSubmitButton();
    }
    var tripWizardSubmitUrl = '/CeylonGo/public/tourist/trip-submit';
    var tripWizardSubmittedKey = 'ceylonTripWizardSubmitted';
    var tripWizardTripIdKey = 'ceylonTripWizardTripId';
    var tripWizardFingerprintKey = 'ceylonTripWizardFingerprint';
    var tripWizardProceedKey = 'ceylonTripWizardProceededToPayment';
    var tripWizardDraftKey = 'ceylonTripWizardDraftV2';
    var tripWizardPendingInitialStep = 1;
    var tripWizardUrlForcedStep = false;
    var tripWizardDraftTimer = null;

    /** Enforce: Verify bookings (12+) only after submit; Payments (13+) only after "Proceed to Payment". */
    function clampWizardStepToAllowed(step) {
      var s = parseInt(step, 10);
      if (isNaN(s) || s < 1) return 1;
      if (s > totalSteps) return totalSteps;
      try { syncTripWizardSubmissionState(); } catch (e) {}
      var tid = '';
      try { tid = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (e1) { tid = ''; }
      var submitted = false;
      try { submitted = sessionStorage.getItem(tripWizardSubmittedKey) === '1'; } catch (e2) {}
      var proceeded = '';
      try { proceeded = String(sessionStorage.getItem(tripWizardProceedKey) || '').trim(); } catch (e3) {}
      if (!submitted || !tid) {
        return Math.min(s, 11);
      }
      if (proceeded !== tid) {
        return Math.min(s, 12);
      }
      return s;
    }

    function fmtTripRefundLabels(paidAtRaw) {
      var paidLabel = '—';
      var deadlineLabel = '—';
      if (!paidAtRaw || !String(paidAtRaw).trim()) {
        return { paidLabel: paidLabel, deadlineLabel: deadlineLabel };
      }
      var raw = String(paidAtRaw).trim();
      var d = new Date(raw.replace(' ', 'T'));
      if (isNaN(d.getTime())) d = new Date(raw);
      if (isNaN(d.getTime())) {
        return { paidLabel: paidLabel, deadlineLabel: deadlineLabel };
      }
      var opts = { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
      paidLabel = d.toLocaleString('en-US', opts);
      var dl = new Date(d.getTime() + 3 * 86400000);
      deadlineLabel = dl.toLocaleString('en-US', opts);
      return { paidLabel: paidLabel, deadlineLabel: deadlineLabel };
    }

    /** Same rules as Trip Overview: card paid, bank slip submitted, or trip marked paid. */
    function deriveTripPaymentFlags(t) {
      if (!t) return { showOverview: false, hasBank: false, isBankSubmitted: false };
      var st = (t.status || '').toString().trim().toLowerCase();
      var paymentState = (t.payment_state || '').toString().trim();
      var hasBank = String(t.bank_transfer_submitted_at || '').trim() !== '';
      var paidComplete = (st === 'confirmed' || st === 'completed') || String(t.payhere_payment_id || '').trim() !== '' || String(t.paid_at || '').trim() !== '';
      if (!paymentState && hasBank && !paidComplete) {
        paymentState = 'payment_submitted';
      }
      var showOverview = paidComplete || paymentState === 'payment_submitted' || hasBank;
      return {
        showOverview: showOverview,
        hasBank: hasBank,
        isBankSubmitted: paymentState === 'payment_submitted'
      };
    }

    function renderTripFinalReviewStep() {
      var mount = document.getElementById('tripFinalReviewMount');
      if (!mount) return;

      mount.innerHTML = '<p style="margin:0;color:#6b7280;">Loading…</p>';

      var tripId = '';
      try { tripId = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (e) { tripId = ''; }
      if (!tripId && typeof serverLastTid !== 'undefined' && serverLastTid > 0) {
        tripId = String(serverLastTid);
        try { sessionStorage.setItem(tripWizardTripIdKey, tripId); } catch (e2) {}
      }
      if (!tripId) {
        mount.innerHTML = '<p style="margin:0;color:#b45309;">Please submit your trip and complete payment first.</p>';
        return;
      }

      fetch('/CeylonGo/public/tourist/trip-payment-status/' + encodeURIComponent(tripId), {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (r) {
          return r.text().then(function (text) {
            var data = null;
            try { data = JSON.parse(text); } catch (eJ) { data = null; }
            return { ok: r.ok, status: r.status, data: data };
          });
        })
        .then(function (res) {
          var data = res.data;
          if (!data || !data.success || !data.trip) {
            var errMsg = (data && data.error) ? data.error : 'Could not load your trip status.';
            if (!res.ok && res.status === 401) {
              errMsg = 'Your session expired. Please sign in again.';
            }
            mount.innerHTML = '<p style="margin:0;color:#b91c1c;">' + escSummaryHtml(errMsg) + '</p>';
            return;
          }
          var t = data.trip;
          var pf = deriveTripPaymentFlags(t);
          if (!pf.showOverview) {
            mount.innerHTML = '<p style="margin:0;color:#b45309;">Payment is not completed yet. If you already paid, please refresh in a moment.</p>';
            return;
          }
          var hasBank = pf.hasBank;
          var isBankSubmitted = pf.isBankSubmitted;

          var paidDate = '';
          if (isBankSubmitted && hasBank) {
            paidDate = fmtTripRefundLabels(t.bank_transfer_submitted_at).paidLabel;
          } else if (String(t.paid_at || '').trim() !== '') {
            paidDate = fmtSummaryDate(t.paid_at);
          } else if (String(t.start_date || '').trim() !== '') {
            paidDate = fmtSummaryDate(t.start_date);
          }
          var dest = String(t.destination || '').trim();
          if (!dest) dest = (((document.getElementById('dest_primary') || {}).value || '') + '').trim();

          var travelers = parseInt(t.number_of_people, 10) || 0;
          if (!travelers) {
            var a = parseInt((document.getElementById('adults') || {}).value, 10) || 0;
            var c = parseInt((document.getElementById('children') || {}).value, 10) || 0;
            var inf = parseInt((document.getElementById('infants') || {}).value, 10) || 0;
            travelers = Math.max(1, a + c + inf);
          }

          var totalLkr = parseFloat(t.budget_lkr) || 0;
          if (!totalLkr) {
            var bud = computeTripBudgetTotals();
            totalLkr = bud && bud.grand ? bud.grand : 0;
          }
          var totalLine = totalLkr ? ('LKR ' + Math.round(totalLkr).toLocaleString('en-LK')) : 'LKR —';
          var tidNum = parseInt(t.id, 10) || parseInt(tripId, 10) || 0;
          var rf = fmtTripRefundLabels(t.paid_at);
          var totalLkrInt = Math.round(totalLkr) || 0;
          var badgeClass = isBankSubmitted ? 'trip-paid-card__badge trip-paid-card__badge--submitted' : 'trip-paid-card__badge';
          var badgeText = isBankSubmitted ? 'Payment submitted' : 'Completed';
          var noteHtml = isBankSubmitted
            ? '<p class="trip-paid-card__note trip-paid-card__note--pending">Your bank transfer was received. We will confirm payment within 1–2 business days.</p>'
            : '<p class="trip-paid-card__note trip-paid-card__note--success">Payment complete. Thank you for choosing Ceylon Go.</p>';
          var actionsHtml = isBankSubmitted
            ? '<div class="trip-paid-card__actions trip-paid-card__actions--single">' +
              '<button type="button" class="trip-paid-card__btn trip-paid-card__btn--primary js-trip-budget-summary-open" data-trip-id="' + escSummaryHtml(String(tidNum || '')) + '">View trip summary</button>' +
              '</div>'
            : '<div class="trip-paid-card__actions trip-paid-card__actions--split">' +
              '<button type="button" class="trip-paid-card__btn trip-paid-card__btn--secondary js-trip-refund-open">Request refund</button>' +
              '<button type="button" class="trip-paid-card__btn trip-paid-card__btn--primary js-trip-budget-summary-open" data-trip-id="' + escSummaryHtml(String(tidNum || '')) + '">View trip summary</button>' +
              '</div>';

          mount.innerHTML =
            '<div class="trip-paid-card trip-paid-card--overview" role="region" aria-label="Trip overview">' +
              '<div class="trip-paid-card__top">' +
                '<span class="' + badgeClass + '">' + escSummaryHtml(badgeText) + '</span>' +
                '<span class="trip-paid-card__date">' + escSummaryHtml(paidDate || '—') + '</span>' +
              '</div>' +
              '<h3 class="trip-paid-card__title">' + escSummaryHtml(dest || 'Your trip') + '</h3>' +
              '<ul class="trip-paid-card__meta">' +
                '<li><strong>Trip No:</strong> ' + escSummaryHtml(tidNum > 0 ? String(tidNum) : '—') + '</li>' +
                '<li><strong>Travelers:</strong> ' + escSummaryHtml(String(travelers)) + '</li>' +
                '<li><strong>Total:</strong> ' + escSummaryHtml(totalLine) + '</li>' +
                '<li><strong>Contact:</strong> ' + escSummaryHtml(tripUserName || '—') + ' · ' + escSummaryHtml(tripUserEmail || '—') + '</li>' +
              '</ul>' +
              noteHtml +
              actionsHtml +
            '</div>';

          var refBtn = mount.querySelector('.js-trip-refund-open');
          var budBtn = mount.querySelector('.js-trip-budget-summary-open');
          if (refBtn && !isBankSubmitted) {
            if (tidNum > 0) refBtn.setAttribute('data-trip-no', String(tidNum));
            refBtn.setAttribute('data-paid-label', rf.paidLabel);
            refBtn.setAttribute('data-deadline-label', rf.deadlineLabel);
            refBtn.setAttribute('data-total-lkr', String(totalLkrInt));
            refBtn.addEventListener('click', function (e) {
              e.preventDefault();
              openTripCustomRefundModal(this);
            });
          }
          if (budBtn) {
            budBtn.addEventListener('click', function (e) {
              e.preventDefault();
              var tid = '';
              try { tid = String(this.getAttribute('data-trip-id') || '').trim(); } catch (eT2) { tid = ''; }
              openTripBudgetSummaryModal(tid);
            });
          }
        })
        .catch(function () {
          mount.innerHTML = '<p style="margin:0;color:#b91c1c;">Network error loading status.</p>';
        });
    }

    var tripCustomRefundState = { tripNo: '', paidLabel: '', deadlineLabel: '', totalLkr: 0 };
    function tripCustomRefundEsc(s) {
      if (s == null) return '';
      var d = document.createElement('div');
      d.textContent = String(s);
      return d.innerHTML;
    }
    function tripCustomRefundNf(n) {
      return Number(n || 0).toLocaleString('en-LK');
    }
    function showTripCustomRefundStep1() {
      var body = document.getElementById('tripCustomRefundModalBody');
      if (!body) return;
      body.innerHTML =
        '<div class="refund-step">' +
        '<p class="refund-policy">Refunds are only possible within <strong>3 days</strong> of your payment (not your travel date).</p>' +
        '<ul class="refund-facts">' +
        '<li><strong>Payment received:</strong> ' + tripCustomRefundEsc(tripCustomRefundState.paidLabel) + '</li>' +
        '<li><strong>Request refund by:</strong> ' + tripCustomRefundEsc(tripCustomRefundState.deadlineLabel) + '</li>' +
        '</ul>' +
        '<p class="refund-hint">If you continue, you confirm a refund request for Trip No ' + tripCustomRefundEsc(tripCustomRefundState.tripNo) + '.</p>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--ghost js-trip-custom-refund-step1-cancel">Cancel</button>' +
        '<button type="button" class="refund-btn refund-btn--primary js-trip-custom-refund-step1-continue">Continue</button>' +
        '</div></div>';
    }
    function showTripCustomRefundStep2() {
      var body = document.getElementById('tripCustomRefundModalBody');
      if (!body) return;
      body.innerHTML =
        '<form class="refund-step" id="tripCustomRefundSubmitForm">' +
        '<input type="hidden" name="trip_id" value="' + tripCustomRefundEsc(tripCustomRefundState.tripNo) + '">' +
        '<p class="refund-confirm-line">Trip No <strong>' + tripCustomRefundEsc(tripCustomRefundState.tripNo) + '</strong></p>' +
        '<p class="refund-confirm-line">Total paid: <strong>LKR ' + tripCustomRefundEsc(tripCustomRefundNf(tripCustomRefundState.totalLkr)) + '</strong></p>' +
        '<label class="refund-label">Reason (optional)' +
        '<textarea name="reason" class="refund-textarea" rows="3" maxlength="2000" placeholder="Tell us why you need a refund"></textarea></label>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--ghost js-trip-custom-refund-step2-back">Back</button>' +
        '<button type="submit" class="refund-btn refund-btn--primary">Submit refund request</button>' +
        '</div></form>';
    }
    function showTripCustomRefundSuccess(msg) {
      var body = document.getElementById('tripCustomRefundModalBody');
      if (!body) return;
      body.innerHTML =
        '<div class="refund-step refund-step--success">' +
        '<p class="refund-success-msg">' + tripCustomRefundEsc(msg) + '</p>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--primary js-trip-custom-refund-done">OK</button>' +
        '</div></div>';
    }
    function openTripCustomRefundModal(btn) {
      var modal = document.getElementById('tripCustomRefundModal');
      if (!btn || !modal) return;
      tripCustomRefundState.tripNo = btn.getAttribute('data-trip-no') || '';
      tripCustomRefundState.paidLabel = btn.getAttribute('data-paid-label') || '';
      tripCustomRefundState.deadlineLabel = btn.getAttribute('data-deadline-label') || '';
      tripCustomRefundState.totalLkr = parseInt(btn.getAttribute('data-total-lkr') || '0', 10) || 0;
      showTripCustomRefundStep1();
      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function closeTripCustomRefundModal() {
      var modal = document.getElementById('tripCustomRefundModal');
      if (!modal) return;
      modal.hidden = true;
      modal.setAttribute('aria-hidden', 'true');
      var bud = document.getElementById('tripBudgetSummaryModalOverlay');
      if (!bud || !bud.classList.contains('trip-modal-open')) {
        document.body.style.overflow = '';
      }
    }
    function openTripBudgetSummaryModal(tripIdOverride) {
      var overlay = document.getElementById('tripBudgetSummaryModalOverlay');
      var inner = document.getElementById('tripBudgetSummaryModalMount');
      if (!overlay || !inner) return;
      inner.innerHTML = '<p class="trip-budget-modal-loading" style="margin:16px 20px;color:#6b7280;">Loading…</p>';
      overlay.classList.add('trip-modal-open');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';

      var tripId = '';
      if (tripIdOverride != null && String(tripIdOverride).trim() !== '') {
        tripId = String(tripIdOverride).trim();
      }
      if (!tripId) {
        try { tripId = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (eT) { tripId = ''; }
      }
      if (!tripId && typeof serverLastTid !== 'undefined' && serverLastTid > 0) tripId = String(serverLastTid);

      function renderNow() {
        renderTripSummaryBudget(inner);
      }

      if (!tripId) {
        renderNow();
        return;
      }

      fetch('/CeylonGo/public/tourist/trip-payment-status/' + encodeURIComponent(tripId), {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (r) {
          return r.json().catch(function () { return null; });
        })
        .then(function (data) {
          if (data && data.success) {
            window.__tripBudgetHydrate = { trip: data.trip || null, snapshot: data.snapshot || null };
          } else {
            window.__tripBudgetHydrate = null;
          }
          renderNow();
        })
        .catch(function () {
          window.__tripBudgetHydrate = null;
          renderNow();
        });
    }
    function closeTripBudgetSummaryModal() {
      var overlay = document.getElementById('tripBudgetSummaryModalOverlay');
      if (!overlay) return;
      overlay.classList.remove('trip-modal-open');
      overlay.setAttribute('aria-hidden', 'true');
      var refM = document.getElementById('tripCustomRefundModal');
      if (!refM || refM.hidden) {
        document.body.style.overflow = '';
      }
      try {
        if (document.body.classList.contains('trip-summary-embed') && window.parent && window.parent !== window) {
          window.parent.postMessage({ type: 'ceylon-trip-summary-closed' }, '*');
        }
      } catch (ePm) {}
    }

    function escapeHtmlDownload(s) {
      return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function downloadTripSummaryReport() {
      var mount = document.getElementById('tripBudgetSummaryModalMount');
      if (!mount) return;
      if (mount.querySelector('.trip-budget-modal-loading')) {
        window.alert('Please wait until your trip summary has finished loading, then try again.');
        return;
      }
      var inner = (mount.innerHTML || '').trim();
      if (!inner) {
        window.alert('Nothing to download yet.');
        return;
      }

      var tripId = '';
      try { tripId = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (e1) { tripId = ''; }
      if (!tripId && typeof serverLastTid !== 'undefined' && serverLastTid > 0) tripId = String(serverLastTid);
      var safeId = tripId.replace(/[^a-zA-Z0-9_-]+/g, '-');
      var generated = new Date();
      var fname = 'CeylonGo-Trip-Summary' + (safeId ? '-' + safeId : '') + '-' + generated.toISOString().slice(0, 10) + '.html';

      var cssHref = '';
      try {
        var linkEl = document.querySelector('link[href*="trip.css"]');
        if (linkEl && linkEl.href) cssHref = linkEl.href;
      } catch (e2) {}
      if (!cssHref) {
        cssHref = String(window.location.origin || '') + '/CeylonGo/public/css/tourist/trip.css';
      }
      var cssLinkTag = '<link rel="stylesheet" href="' + escapeHtmlDownload(cssHref) + '">\n';

      var fallbackCss = '<style>' +
        'html{background:#e8eaed;min-height:100%}' +
        'body.dl-trip-report{max-width:min(920px,100%);margin:24px auto;padding:24px 28px 48px;box-sizing:border-box;' +
        'font-family:system-ui,Segoe UI,sans-serif;color:#111;background:#fafafa;line-height:1.45;' +
        'box-shadow:0 0 0 1px rgba(0,0,0,0.06),0 4px 24px rgba(0,0,0,0.06);border-radius:12px}' +
        'body.dl-trip-report .trip-summary-detailed-root{width:100%;max-width:100%}' +
        'body.dl-trip-report .trip-summary-detailed-layout{max-width:100%;grid-template-columns:1fr}' +
        'body.dl-trip-report .trip-summary-budget-aside{position:static;margin-top:20px;max-width:100%}' +
        '.dl-report-head{margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid #2c5530}' +
        '.dl-report-meta{color:#64748b;font-size:14px;margin:0}' +
        '.dl-report-foot{margin-top:32px;padding-top:14px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280}' +
        'main img{max-width:100%;height:auto}' +
        '</style>\n';

      var metaText = 'Generated ' + generated.toLocaleString();
      if (tripId) metaText += ' · Trip #' + tripId;

      var doc = '<!DOCTYPE html>\n<html lang="en">\n<head>\n<meta charset="utf-8">\n<meta name="viewport" content="width=device-width, initial-scale=1">\n' +
        '<title>Ceylon Go - Trip Summary &amp; Budget</title>\n' +
        cssLinkTag +
        fallbackCss +
        '</head>\n<body class="dl-trip-report">\n' +
        '<header class="dl-report-head"><h1 style="margin:0 0 8px;font-size:1.45rem;color:#2c5530;">Ceylon Go - Trip Summary &amp; Budget</h1>' +
        '<p class="dl-report-meta">' + escapeHtmlDownload(metaText) + '</p></header>\n' +
        '<main class="trip-summary-detailed-root">' + inner + '</main>\n' +
        '<footer class="dl-report-foot"><p>This HTML file is a snapshot of your trip summary. If styling looks plain, open it while online so the stylesheet can load, or use your browser&rsquo;s Print dialog to save as PDF.</p></footer>\n' +
        '</body>\n</html>';

      try {
        var blob = new Blob([doc], { type: 'text/html;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = fname;
        a.rel = 'noopener';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      } catch (e3) {
        window.alert('Could not create the download. Try again or use Print from your browser.');
      }
    }

    function tripWizardFingerprint() {
      var d1 = ((document.getElementById('dest_primary') || {}).value || '').trim();
      var s1 = ((document.getElementById('start_date') || {}).value || '').trim();
      var e1 = ((document.getElementById('end_date') || {}).value || '').trim();
      var d2 = ((document.getElementById('dest_primary_2') || {}).value || '').trim();
      var s2 = ((document.getElementById('start_date_2') || {}).value || '').trim();
      var e2 = ((document.getElementById('end_date_2') || {}).value || '').trim();
      var d3 = ((document.getElementById('dest_primary_3') || {}).value || '').trim();
      var s3 = ((document.getElementById('start_date_3') || {}).value || '').trim();
      var e3 = ((document.getElementById('end_date_3') || {}).value || '').trim();
      var a = ((document.getElementById('adults') || {}).value || '').trim();
      var c = ((document.getElementById('children') || {}).value || '').trim();
      var inf = ((document.getElementById('infants') || {}).value || '').trim();
      var sec = ((document.getElementById('add_another_destination') || {}).value || '').trim();
      var thr = ((document.getElementById('add_third_destination') || {}).value || '').trim();
      return [
        'd1=' + d1, 's1=' + s1, 'e1=' + e1,
        'sec=' + sec, 'd2=' + d2, 's2=' + s2, 'e2=' + e2,
        'thr=' + thr, 'd3=' + d3, 's3=' + s3, 'e3=' + e3,
        'a=' + a, 'c=' + c, 'i=' + inf
      ].join('|');
    }

    function syncTripWizardSubmissionState() {
      // Prevent "Submitted" from carrying over to a different trip in the same tab/session.
      // Do not drop trip id after PayHere: full page reload leaves an empty form fingerprint.
      try {
        var currentFp = tripWizardFingerprint();
        var storedFp = sessionStorage.getItem(tripWizardFingerprintKey) || '';
        var submitted = sessionStorage.getItem(tripWizardSubmittedKey) === '1';
        var tid = sessionStorage.getItem(tripWizardTripIdKey) || '';
        var proceededFor = sessionStorage.getItem(tripWizardProceedKey) || '';
        var paymentLocked = (tid !== '' && proceededFor === tid);
        if (submitted && storedFp && storedFp !== currentFp) {
          sessionStorage.removeItem(tripWizardSubmittedKey);
          if (!paymentLocked) {
            sessionStorage.removeItem(tripWizardTripIdKey);
            sessionStorage.removeItem(tripWizardProceedKey);
          }
        }
        sessionStorage.setItem(tripWizardFingerprintKey, currentFp);
      } catch (e) {}
    }
    function bindTripWizardSubmitButton() {
      syncTripWizardSubmissionState();
      var btn = document.getElementById('tripWizardSubmitBtn');
      var okEl = document.getElementById('tripWizardSubmitSuccess');
      if (!btn) return;
      var done = sessionStorage.getItem(tripWizardSubmittedKey) === '1';
      if (done) {
        btn.disabled = true;
        btn.textContent = 'Submitted';
        if (okEl) okEl.hidden = false;
      } else {
        btn.disabled = false;
        btn.textContent = 'Submit trip';
        if (okEl) okEl.hidden = true;
      }
      btn.onclick = function () {
        if (sessionStorage.getItem(tripWizardSubmittedKey) === '1') return;
        var destEl = document.getElementById('dest_primary');
        var startEl = document.getElementById('start_date');
        var endEl = document.getElementById('end_date');
        var fd = new FormData();
        fd.append('destination', destEl ? (destEl.value || '').trim() : '');
        fd.append('start_date', startEl ? (startEl.value || '').trim() : '');
        fd.append('end_date', endEl ? (endEl.value || '').trim() : '');
        fd.append('customer_name', tripUserName || '');
        var a = parseInt((document.getElementById('adults') || {}).value, 10) || 0;
        var c = parseInt((document.getElementById('children') || {}).value, 10) || 0;
        var inf = parseInt((document.getElementById('infants') || {}).value, 10) || 0;
        fd.append('number_of_people', String(Math.max(1, a + c + inf)));
        var numDays = 1;
        if (startEl && endEl && startEl.value && endEl.value) {
          var d0 = new Date(String(startEl.value).trim() + 'T12:00:00');
          var d1 = new Date(String(endEl.value).trim() + 'T12:00:00');
          if (!isNaN(d0.getTime()) && !isNaN(d1.getTime()) && d1 >= d0) {
            numDays = Math.round((d1 - d0) / 86400000) + 1;
          }
        }
        fd.append('number_of_days', String(Math.max(1, numDays)));
        var budSubmit = computeTripBudgetTotals();
        fd.append('budget_lkr', String(Math.round(budSubmit.grand)));

        // Collect queued tour guide requests (do not persist until submit trip).
        try {
          var reqs = [];
          var listsG = ['tripStopsList', 'tripStopsList_2', 'tripStopsList_3'];
          listsG.forEach(function (lid) {
            var list = document.getElementById(lid);
            if (!list) return;
            list.querySelectorAll('.trip-stop-card').forEach(function (card) {
              var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
              var gOn = gYes && gYes.classList.contains('selected');
              if (!gOn) return;
              var queued = (card.getAttribute('data-guide-queued') || '').trim() === '1';
              var loc = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
              var dt = ((card.querySelector('.trip-stop-guide-date') || {}).value || '').trim();
              var lang = ((card.querySelector('.trip-stop-guide-language') || {}).value || '').trim();
              var tm = ((card.querySelector('.trip-stop-guide-time') || {}).value || '').trim();
              var notes = ((card.querySelector('.trip-stop-guide-notes') || {}).value || '').trim();
              if (!queued || !loc || !dt || !lang || !tm) return;
              reqs.push({ location: loc, date: dt, language: lang, time: tm, notes: notes });
            });
          });
          if (reqs.length) fd.append('guide_requests', JSON.stringify(reqs));
        } catch (eG) {}
        try {
          fd.append('wizard_snapshot', JSON.stringify(collectWizardSnapshotForSubmit()));
        } catch (eW) {}

        btn.disabled = true;
        btn.textContent = 'Submitting…';
        fetch(tripWizardSubmitUrl, {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        })
          .then(function (r) { return r.json().catch(function () { return null; }); })
          .then(function (data) {
            if (data && data.success) {
              try { clearTripWizardDraft(); } catch (eClr) {}
              sessionStorage.setItem(tripWizardSubmittedKey, '1');
              try { sessionStorage.setItem(tripWizardFingerprintKey, tripWizardFingerprint()); } catch (e) {}
              if (data.trip_id != null) {
                sessionStorage.setItem(tripWizardTripIdKey, String(data.trip_id));
              }
              btn.disabled = true;
              btn.textContent = 'Submitted';
              if (okEl) { okEl.hidden = false; }
              if (data.budget_persisted === false && data.message) {
                console.warn(data.message);
              }
              // After submitting, move to Verify bookings step (before Payments).
              try { showStep(12); } catch (e) {}
            } else {
              btn.disabled = false;
              btn.textContent = 'Submit trip';
              alert((data && data.error) ? data.error : 'Could not submit your trip. Please try again.');
            }
          })
          .catch(function () {
            btn.disabled = false;
            btn.textContent = 'Submit trip';
            alert('Network error. Please try again.');
          });
      };
    }
    function showStep(step) {
      syncTripWizardSubmissionState();
      currentStep = step;
      panels.forEach(function (p) { p.classList.toggle('active', parseInt(p.getAttribute('data-step'), 10) === step); });
      stepLabels.forEach(function (el, i) {
        var label = el.querySelector('.trip-step-label');
        if (label) label.classList.toggle('active', i === step - 1);
        var line = el.querySelector('.trip-step-line');
        if (line) line.classList.toggle('active', i === step - 1);
      });
      if (btnPrev) btnPrev.disabled = step <= 1;
      if (step === 3) filterAccommodationByDestination('1');
      if (step === 6) filterAccommodationByDestination('2');
      if (step === 9) filterAccommodationByDestination('3');
      if (step === 5 && !secondStopsInitialized) {
        secondStopsInitialized = true;
        syncSecondDestinationDefaultDates();
        initDestAutocomplete2();
        var list2 = document.getElementById('tripStopsList_2');
        if (list2 && list2.querySelectorAll('.trip-stop-card').length === 0) addStopCard(null, { listId: 'tripStopsList_2' });
        updateAddStopsButtonLabel2();
      }
      if (step === 8 && !thirdStopsInitialized) {
        thirdStopsInitialized = true;
        syncThirdDestinationDefaultDates();
        initDestAutocomplete3();
        var list3 = document.getElementById('tripStopsList_3');
        if (list3 && list3.querySelectorAll('.trip-stop-card').length === 0) addStopCard(null, { listId: 'tripStopsList_3' });
        updateAddStopsButtonLabel3();
      }
      if (step === 10) renderTripSummaryBudget();
      if (step === 11) renderTripSummaryItinerary();
      if (step === 12) renderVerifyBookingsStep();
      if (step === 13) {
        initTripPaymentStepUi();
        renderTripPaymentsStep();
      }
      if (step === 14) {
        renderTripFinalReviewStep();
      }
      try { scheduleSaveTripWizardDraft(); } catch (eDraft) {}
    }

    function tripWizardDraftShouldRestore() {
      try {
        if (sessionStorage.getItem(tripWizardSubmittedKey) === '1') return false;
        var qs = new URLSearchParams(window.location.search || '');
        if (qs.get('afterPayment') === '1') return false;
        var urlTid = parseInt(qs.get('trip_id') || '0', 10) || 0;
        if (urlTid > 0) return false;
        return true;
      } catch (e) {
        return false;
      }
    }
    function clearTripWizardDraft() {
      try { localStorage.removeItem(tripWizardDraftKey); } catch (e) {}
    }
    function serializeTripWizardStopCard(card) {
      function tv(sel) {
        var el = card.querySelector(sel);
        return el ? String(el.value || '').trim() : '';
      }
      function selGroup(selRoot) {
        var y = card.querySelector(selRoot + ' .trip-toggle-btn.selected');
        return y ? String(y.getAttribute('data-value') || '') : '';
      }
      var locEl = card.querySelector('.trip-stop-location');
      return {
        location: tv('.trip-stop-location'),
        placeId: locEl && locEl.dataset && locEl.dataset.placeId ? String(locEl.dataset.placeId) : '',
        transportNeeded: selGroup('.trip-stop-opt-transport'),
        tourGuideNeeded: selGroup('.trip-stop-opt-guide'),
        pickup: tv('.trip-stop-pickup'),
        dropoff: tv('.trip-stop-dropoff'),
        fareAmount: tv('.trip-stop-fare-amount'),
        trDate: tv('.trip-stop-tr-date'),
        trVehicle: tv('.trip-stop-tr-vehicle'),
        trTime: tv('.trip-stop-tr-time'),
        trPeople: tv('.trip-stop-tr-people'),
        guideLocation: tv('.trip-stop-guide-location'),
        guideDate: tv('.trip-stop-guide-date'),
        guideLanguage: tv('.trip-stop-guide-language'),
        guideTime: tv('.trip-stop-guide-time'),
        guideNotes: tv('.trip-stop-guide-notes')
      };
    }
    function serializeTripWizardStopLists() {
      function collect(listId) {
        var list = document.getElementById(listId);
        if (!list) return [];
        return Array.prototype.map.call(list.querySelectorAll('.trip-stop-card'), serializeTripWizardStopCard);
      }
      return {
        tripStopsList: collect('tripStopsList'),
        tripStopsList_2: collect('tripStopsList_2'),
        tripStopsList_3: collect('tripStopsList_3')
      };
    }
    function buildTripWizardDraftPayload() {
      var main = document.querySelector('main.trip-main-content');
      var fields = {};
      if (main) {
        main.querySelectorAll('input, select, textarea').forEach(function (el) {
          var id = el.id;
          if (!id) return;
          var type = (el.type || '').toLowerCase();
          if (type === 'file' || type === 'button' || type === 'submit' || type === 'reset') return;
          if (type === 'checkbox' || type === 'radio') {
            fields[id] = el.checked ? (el.value || '1') : '';
            return;
          }
          fields[id] = el.value;
        });
      }
      var tripTypeEl = document.getElementById('trip_type');
      var anotherEl = document.getElementById('add_another_destination');
      var thirdEl = document.getElementById('add_third_destination');
      return {
        v: 2,
        savedAt: Date.now(),
        step: currentStep,
        fields: fields,
        stops: serializeTripWizardStopLists(),
        tripType: tripTypeEl ? String(tripTypeEl.value || '') : '',
        anotherDest: anotherEl ? String(anotherEl.value || '') : '',
        thirdDest: thirdEl ? String(thirdEl.value || '') : ''
      };
    }
    function saveTripWizardDraftNow() {
      if (!tripWizardDraftShouldRestore()) return;
      try {
        var payload = buildTripWizardDraftPayload();
        localStorage.setItem(tripWizardDraftKey, JSON.stringify(payload));
      } catch (e) {
        try { console.warn('Trip wizard draft save failed', e); } catch (e2) {}
      }
    }
    function scheduleSaveTripWizardDraft() {
      if (!tripWizardDraftShouldRestore()) return;
      clearTimeout(tripWizardDraftTimer);
      tripWizardDraftTimer = setTimeout(saveTripWizardDraftNow, 450);
    }
    function patchTripWizardStopCardFromDraft(card, d) {
      if (!card || !d) return;
      function set(sel, v) {
        var el = card.querySelector(sel);
        if (el) el.value = v != null ? String(v) : '';
      }
      set('.trip-stop-tr-date', d.trDate);
      set('.trip-stop-tr-vehicle', d.trVehicle);
      set('.trip-stop-tr-time', d.trTime);
      set('.trip-stop-tr-people', d.trPeople);
      set('.trip-stop-guide-date', d.guideDate);
      set('.trip-stop-guide-language', d.guideLanguage);
      set('.trip-stop-guide-time', d.guideTime);
      set('.trip-stop-guide-notes', d.guideNotes);
      var loc = card.querySelector('.trip-stop-location');
      if (loc && d.placeId) loc.dataset.placeId = d.placeId;
      try {
        syncStopCardFields(card);
        updateStopCardSummary(card);
      } catch (e) {}
    }
    function restoreTripWizardStopList(listId, rows) {
      var list = document.getElementById(listId);
      if (!list) return;
      list.innerHTML = '';
      if (listId === 'tripStopsList_3') stopIndex3 = 0;
      else if (listId === 'tripStopsList_2') stopIndex2 = 0;
      else stopIndex = 0;
      (rows || []).forEach(function (row) {
        var d = row || {};
        addStopCard({
          location: d.location || '',
          transportNeeded: d.transportNeeded || '',
          tourGuideNeeded: d.tourGuideNeeded || '',
          pickup: d.pickup || '',
          dropoff: d.dropoff || '',
          guideLocation: d.guideLocation || '',
          fareAmount: d.fareAmount || ''
        }, { listId: listId });
        var cards = list.querySelectorAll('.trip-stop-card');
        var card = cards[cards.length - 1];
        patchTripWizardStopCardFromDraft(card, d);
      });
    }
    function applyTripWizardDraft(raw) {
      if (!raw || raw.v !== 2 || !raw.fields) return;
      var F = raw.fields;
      Object.keys(F).forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var type = (el.type || '').toLowerCase();
        if (type === 'checkbox' || type === 'radio') {
          el.checked = String(F[id]) !== '';
          return;
        }
        if (type === 'file') return;
        el.value = F[id] != null ? String(F[id]) : '';
      });
      if (raw.stops) {
        restoreTripWizardStopList('tripStopsList', raw.stops.tripStopsList);
        restoreTripWizardStopList('tripStopsList_2', raw.stops.tripStopsList_2);
        restoreTripWizardStopList('tripStopsList_3', raw.stops.tripStopsList_3);
      }
      var tt = raw.tripType || ((document.getElementById('trip_type') || {}).value || '');
      var hidTt = document.getElementById('trip_type');
      if (hidTt && tt) {
        hidTt.value = tt;
        document.querySelectorAll('.trip-step-panel[data-step="1"] .trip-type-card').forEach(function (c) {
          var on = (c.getAttribute('data-type') || '') === tt;
          c.classList.toggle('selected', on);
        });
      }
      var av = raw.anotherDest || '';
      var hidA = document.getElementById('add_another_destination');
      if (hidA && av) {
        hidA.value = av;
        var p4 = document.querySelector('.trip-step-panel[data-step="4"]');
        if (p4) {
          p4.querySelectorAll('.trip-another-dest-card').forEach(function (c) {
            var on = (c.getAttribute('data-value') || '') === av;
            c.classList.toggle('selected', on);
            c.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
        }
      }
      var tv = raw.thirdDest || '';
      var hid3 = document.getElementById('add_third_destination');
      if (hid3 && tv) {
        hid3.value = tv;
        var p7 = document.querySelector('.trip-step-panel[data-step="7"]');
        if (p7) {
          p7.querySelectorAll('.trip-another-dest-card').forEach(function (c) {
            var on = (c.getAttribute('data-value') || '') === tv;
            c.classList.toggle('selected', on);
            c.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
        }
      }
      try {
        updateStopsHeading();
        updateStopsHeading2();
        updateStopsHeading3();
      } catch (eH) {}
    }
    function loadTripWizardDraft() {
      try {
        var s = localStorage.getItem(tripWizardDraftKey);
        if (!s) return null;
        return JSON.parse(s);
      } catch (e) {
        return null;
      }
    }

    function mapVerifyBookingStatus(raw) {
      var s = (raw || '').toString().trim().toLowerCase();
      if (!s) return { text: '—', cls: 'trip-sum-status--muted' };
      if (s === 'pending') return { text: 'Pending', cls: 'trip-sum-status--pending' };
      if (s === 'confirmed' || s === 'completed' || s === 'approved' || s === 'accepted') {
        return { text: 'Accepted', cls: 'trip-sum-status--accepted' };
      }
      if (s === 'cancelled' || s === 'canceled') return { text: 'Cancelled', cls: 'trip-sum-status--cancelled' };
      return { text: String(raw), cls: 'trip-sum-status--muted' };
    }

    function mapVerifyTripStatus(raw) {
      var s = (raw || '').toString().trim().toLowerCase();
      if (!s) return { text: 'Pending', cls: 'trip-sum-status--pending' };
      if (s === 'pending') return { text: 'Pending', cls: 'trip-sum-status--pending' };
      if (s === 'confirmed' || s === 'completed' || s === 'approved' || s === 'accepted') {
        return { text: 'Accepted', cls: 'trip-sum-status--accepted' };
      }
      if (s === 'cancelled' || s === 'canceled') return { text: 'Cancelled', cls: 'trip-sum-status--cancelled' };
      if (s === 'rejected' || s === 'declined' || s === 'denied') return { text: 'Rejected', cls: 'trip-sum-status--cancelled' };
      return { text: String(raw), cls: 'trip-sum-status--muted' };
    }

    function isVerifyTripAccepted(raw) {
      var s = (raw || '').toString().trim().toLowerCase();
      return s === 'confirmed' || s === 'completed' || s === 'approved' || s === 'accepted';
    }

    function isVerifyBookingApproved(raw) {
      var s = (raw || '').toString().trim().toLowerCase();
      return s === 'confirmed' || s === 'completed' || s === 'approved' || s === 'accepted';
    }

    function getVerifyBookingsLegs() {
      var legs = [];
      legs.push({ num: 1, stopsId: 'tripStopsList', accBodyId: 'tripAccommodationSummaryBody' });
      if (typeof wantsSecondDestination === 'function' && wantsSecondDestination()) {
        legs.push({ num: 2, stopsId: 'tripStopsList_2', accBodyId: 'trip2AccommodationSummaryBody' });
      }
      if (typeof wantsThirdDestination === 'function' && wantsThirdDestination()) {
        legs.push({ num: 3, stopsId: 'tripStopsList_3', accBodyId: 'trip3AccommodationSummaryBody' });
      }
      return legs;
    }

    /** Transport / guide / hotel rows must be approved (not pending) before Payments / Next. */
    function validateVerifyBookingsApproved() {
      var legs = getVerifyBookingsLegs();
      var globalStopIdx = 0;
      for (var li = 0; li < legs.length; li++) {
        var leg = legs[li];
        var stopsEl = document.getElementById(leg.stopsId);
        if (stopsEl) {
          var cards = stopsEl.querySelectorAll('.trip-stop-card');
          for (var ci = 0; ci < cards.length; ci++) {
            var card = cards[ci];
            globalStopIdx++;
            var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
            var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
            var trOn = trYes && trYes.classList.contains('selected');
            var gOn = gYes && gYes.classList.contains('selected');
            if (trOn) {
              var ts = (card.getAttribute('data-transport-booking-status') || '').trim();
              if (!isVerifyBookingApproved(ts)) {
                return {
                  ok: false,
                  message: 'Transport for stop ' + globalStopIdx + ' is not approved yet. Please wait for approval before continuing.'
                };
              }
            }
            if (gOn) {
              var gs = (card.getAttribute('data-guide-booking-status') || '').trim();
              if (!isVerifyBookingApproved(gs)) {
                return {
                  ok: false,
                  message: 'Tour guide for stop ' + globalStopIdx + ' is not approved yet. Please wait for approval before continuing.'
                };
              }
            }
          }
        }
        var accBody = document.getElementById(leg.accBodyId);
        var items = accBody ? accBody.querySelectorAll('.trip-accommodation-summary-item') : [];
        for (var ii = 0; ii < items.length; ii++) {
          var item = items[ii];
          var bid = item.getAttribute('data-booking-id');
          var bst = (item.getAttribute('data-booking-status') || '').trim();
          if (bid && !isVerifyBookingApproved(bst)) {
            return {
              ok: false,
              message: 'A hotel booking is still pending approval. Please wait until it is approved before continuing.'
            };
          }
        }
      }
      return { ok: true };
    }

    function renderVerifyBookingsStep() {
      var mount = document.getElementById('tripVerifyBookingsMount');
      if (!mount) return;

      // Never leave this step visually empty.
      mount.innerHTML =
        '<div class="trip-sum-budget-card trip-sum-booking-status-card" role="region" aria-label="Verify bookings">' +
        '<div class="trip-sum-budget-card-head">Status of Bookings</div>' +
        '<p style="margin:14px 0 0 0; color:#555; line-height:1.5;">Loading…</p>' +
        '</div>';

      try {
        syncTripWizardSubmissionState();
        var tripId = '';
        try { tripId = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (eTid) { tripId = ''; }

        var pendingCount = 0;
        var acceptedCount = 0;
        var cancelledCount = 0;
        function bump(st) {
          var t = (st || '').toString().trim().toLowerCase();
          if (t === 'pending') pendingCount++;
          else if (t === 'confirmed' || t === 'completed' || t === 'approved' || t === 'accepted') acceptedCount++;
          else if (t === 'cancelled' || t === 'canceled') cancelledCount++;
        }

        var rows = [];
        var globalStopIdx = 0;
        var legs = getVerifyBookingsLegs();

        legs.forEach(function (leg) {
          var stopsEl = document.getElementById(leg.stopsId);
          if (stopsEl) {
            stopsEl.querySelectorAll('.trip-stop-card').forEach(function (card) {
              globalStopIdx++;
              var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
              var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
              var trOn = trYes && trYes.classList.contains('selected');
              var gOn = gYes && gYes.classList.contains('selected');
              if (trOn) {
                var ts = (card.getAttribute('data-transport-booking-status') || '').trim();
                var disp = ts ? mapVerifyBookingStatus(ts) : { text: 'Not requested', cls: 'trip-sum-status--muted' };
                if (ts) bump(ts);
                rows.push({ label: 'Stop ' + globalStopIdx + ' — Transport provider', disp: disp });
              }
              if (gOn) {
                var gs = (card.getAttribute('data-guide-booking-status') || '').trim();
                var dispG = gs ? mapVerifyBookingStatus(gs) : { text: 'Not requested', cls: 'trip-sum-status--muted' };
                if (gs) bump(gs);
                rows.push({ label: 'Stop ' + globalStopIdx + ' — Tour guide', disp: dispG });
              }
            });
          }

          var accBody = document.getElementById(leg.accBodyId);
          var items = accBody ? accBody.querySelectorAll('.trip-accommodation-summary-item') : [];
          if (!items.length) {
            rows.push({
              label: 'Destination ' + leg.num + ' — Hotel',
              disp: { text: 'Not booked', cls: 'trip-sum-status--muted' }
            });
          } else {
            items.forEach(function (item) {
              var ps = item.querySelectorAll('p');
              var hn = '';
              for (var pi = 0; pi < ps.length; pi++) {
                var tx = (ps[pi].textContent || '');
                if (/^Hotel:/i.test(tx)) {
                  hn = tx.replace(/^Hotel:\s*/i, '').trim();
                  break;
                }
              }
              var bid = item.getAttribute('data-booking-id');
              var bst = (item.getAttribute('data-booking-status') || '').trim();
              var lab = 'Destination ' + leg.num + ' — ' + (hn ? hn : 'Hotel');
              var dispH;
              if (bid) {
                dispH = mapVerifyBookingStatus(bst || 'pending');
                bump(bst || 'pending');
              } else {
                dispH = { text: 'Not booked', cls: 'trip-sum-status--muted' };
              }
              rows.push({ label: lab, disp: dispH });
            });
          }
        });

        // Trip acceptance still comes from DB (footnote + Proceed); list rows are transport/hotel only.
        var tripStatusRaw = '';
        if (!tripId) {
          finalizeRender(false);
        } else {
          fetch('/CeylonGo/public/tourist/trip-payment-status/' + encodeURIComponent(tripId), {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          })
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (data && data.success && data.trip) {
                tripStatusRaw = (data.trip.status || '').toString().trim();
              } else {
                tripStatusRaw = '';
              }
              var acceptedTrip = isVerifyTripAccepted(tripStatusRaw);
              finalizeRender(acceptedTrip);
            })
            .catch(function () {
              finalizeRender(false);
            });
        }

        function finalizeRender(tripAccepted) {
          var linesHtml = rows.map(function (r) {
            return '<li><span>' + escSummaryHtml(r.label) + '</span><strong class="trip-sum-status ' + r.disp.cls + '">' + escSummaryHtml(r.disp.text) + '</strong></li>';
          }).join('');
          var summaryText = 'No confirmations yet';
          if (pendingCount || acceptedCount || cancelledCount) {
            var parts = [];
            if (pendingCount) parts.push(pendingCount + ' pending');
            if (acceptedCount) parts.push(acceptedCount + ' accepted');
            if (cancelledCount) parts.push(cancelledCount + ' cancelled');
            summaryText = parts.join(' · ');
          }
          var topNote = '';
          if (!tripId) {
            topNote = '<p class="trip-sum-budget-footnote">Please submit your trip first to track its acceptance status.</p>';
          } else if (!tripAccepted) {
            topNote = '<p class="trip-sum-budget-footnote">Status updates can take up to <strong>24 hours</strong>. Please wait until your submitted trip is marked <strong>Accepted</strong> before making payment. You’ll be notified once everything is ready.</p>';
          } else {
            topNote = '<p class="trip-sum-budget-footnote">Your submitted trip is <strong>Accepted</strong>. You can proceed to payment.</p>';
          }

          mount.innerHTML =
            '<div style="max-width: 520px; margin: 0 auto; width: 100%;">' +
              '<div class="trip-sum-budget-card trip-sum-booking-status-card" role="region" aria-label="Status of bookings">' +
                '<div class="trip-sum-budget-card-head">Status of Bookings</div>' +
                '<ul class="trip-sum-budget-lines">' + linesHtml + '</ul>' +
                '<div class="trip-sum-budget-total trip-sum-booking-status-total"><span>Summary</span><strong class="trip-sum-booking-status-total-value">' + escSummaryHtml(summaryText) + '</strong></div>' +
                topNote +
                '<div class="trip-sum-booking-submit-wrap">' +
                  '<button type="button" class="trip-sum-booking-submit-btn" id="tripVerifyProceedBtn"' + (tripAccepted ? '' : ' disabled') + '>Proceed to Payment</button>' +
                '</div>' +
              '</div>' +
            '</div>';

          var proceedBtn = document.getElementById('tripVerifyProceedBtn');
          if (proceedBtn) {
            proceedBtn.onclick = function () {
              var tid = '';
              try { tid = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (e0) { tid = ''; }
              if (!tid) {
                alert('Please click "Submit trip" first, then proceed to payment.');
                showStep(11);
                return;
              }
              fetch('/CeylonGo/public/tourist/trip-payment-status/' + encodeURIComponent(tid), {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
              })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                  var st = (data && data.trip && data.trip.status) ? String(data.trip.status) : '';
                  if (!isVerifyTripAccepted(st)) {
                    alert('Your submitted trip is not accepted yet. Please wait (up to 24 hours) and try again.');
                    return;
                  }
                  try { sessionStorage.setItem(tripWizardProceedKey, String(tid)); } catch (e) {}
                  showStep(13);
                })
                .catch(function () {
                  alert('Could not verify your trip status right now. Please try again.');
                });
            };
          }
        }
      } catch (e) {
        mount.innerHTML =
          '<div class="trip-sum-budget-card trip-sum-booking-status-card" role="region" aria-label="Verify bookings">' +
          '<div class="trip-sum-budget-card-head">Status of Bookings</div>' +
          '<p style="margin:14px 0 0 0; color:#b91c1c; line-height:1.5;">Could not load booking status. ' + escSummaryHtml(e && e.message ? e.message : 'Unknown error') + '</p>' +
          '</div>';
      }
    }
    function fmtTripLkr(n) {
      var v = Math.round(Number(n) || 0);
      return 'LKR ' + v.toLocaleString('en-LK');
    }
    function fmtTripDateShort(iso) {
      if (!iso || !String(iso).trim()) return '';
      var x = new Date(String(iso).trim() + 'T12:00:00');
      if (isNaN(x.getTime())) return String(iso);
      return x.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
    function renderTripPaymentsStep() {
      var root = document.getElementById('tripPaymentStepRoot');
      if (!root) return;
      var bud = computeTripBudgetTotals();
      var grand = Math.round(bud.grand);
      var elDest = document.getElementById('tripPaymentSummaryDest');
      var elDates = document.getElementById('tripPaymentSummaryDates');
      var elParty = document.getElementById('tripPaymentSummaryParty');
      var elTot = document.getElementById('tripPaymentTotalLine');
      var elBankAmt = document.getElementById('trip_bank_amount_line');
      if (elDest) elDest.textContent = ((document.getElementById('dest_primary') || {}).value || '').trim() || '—';
      var sd = (document.getElementById('start_date') || {}).value || '';
      var ed = (document.getElementById('end_date') || {}).value || '';
      if (elDates) {
        if (sd && ed) {
          elDates.textContent = fmtTripDateShort(sd) + ' — ' + fmtTripDateShort(ed);
        } else {
          elDates.textContent = '—';
        }
      }
      var a = parseInt((document.getElementById('adults') || {}).value, 10) || 0;
      var c = parseInt((document.getElementById('children') || {}).value, 10) || 0;
      var inf = parseInt((document.getElementById('infants') || {}).value, 10) || 0;
      var totalP = Math.max(1, a + c + inf);
      if (elParty) {
        var parts = [];
        if (a) parts.push(a + ' adult' + (a !== 1 ? 's' : ''));
        if (c) parts.push(c + ' child' + (c !== 1 ? 'ren' : ''));
        if (inf) parts.push(inf + ' infant' + (inf !== 1 ? 's' : ''));
        elParty.textContent = parts.length ? (String(totalP) + ' (' + parts.join(', ') + ')') : String(totalP);
      }
      if (elTot) elTot.textContent = fmtTripLkr(grand);
      if (elBankAmt) elBankAmt.textContent = fmtTripLkr(grand);
      var tripId = sessionStorage.getItem(tripWizardTripIdKey) || '';
      var hid = document.getElementById('trip_payment_trip_id');
      if (hid) hid.value = tripId;
      var elBankRef = document.getElementById('trip_bank_ref_id');
      if (elBankRef) elBankRef.textContent = tripId || '—';
      var hint = document.getElementById('tripPaymentTripHint');
      if (hint) hint.style.display = tripId ? 'none' : 'block';
      var maxCap = parseInt(root.getAttribute('data-payhere-max') || '0', 10);
      var card = document.getElementById('trip_pay_card');
      var bank = document.getElementById('trip_pay_bank');
      var cardBlocked = maxCap > 0 && grand > maxCap;
      var pPay = document.getElementById('trip_payhere_panel');
      var pBank = document.getElementById('trip_bank_panel');
      var btn = document.getElementById('trip_payment_submit_btn');
      var limBanner = document.getElementById('trip_payhere_limit_banner');
      var cardLbl = document.getElementById('trip_pay_card_label');
      if (card && bank) {
        if (cardBlocked) {
          card.disabled = true;
          card.checked = false;
          bank.checked = true;
        } else {
          card.disabled = false;
          if (!bank.checked) card.checked = true;
        }
      }
      if (cardLbl) {
        cardLbl.textContent = cardBlocked ? 'Credit / Debit Card (over per-payment limit)' : 'Credit / Debit Card';
        cardLbl.style.opacity = cardBlocked ? '0.65' : '1';
      }
      if (limBanner) {
        if (cardBlocked && maxCap > 0) {
          limBanner.style.display = 'block';
          limBanner.innerHTML = 'This total (' + fmtTripLkr(grand) + ') is above the online card limit for this payment account (LKR ' + maxCap.toLocaleString('en-LK') + ' per payment). Choose <strong>Bank transfer</strong>, or your merchant must raise the limit in <strong>PayHere</strong> (plan / settings) to accept card payments this large.';
        } else {
          limBanner.style.display = 'none';
          limBanner.innerHTML = '';
        }
      }
      var isBank = bank && bank.checked;
      if (cardBlocked) {
        if (pPay) pPay.style.display = 'none';
        if (pBank) pBank.style.display = 'block';
      } else {
        if (pPay) pPay.style.display = isBank ? 'none' : 'block';
        if (pBank) pBank.style.display = isBank ? 'block' : 'none';
      }
      var payLabel = 'Pay LKR ' + grand.toLocaleString('en-LK');
      if (btn) {
        btn.textContent = (cardBlocked || isBank) ? 'Continue' : payLabel;
        btn.disabled = !tripId;
      }
    }
    function initTripPaymentStepUi() {
      if (window.tripPaymentUiInited) return;
      window.tripPaymentUiInited = true;
      var card = document.getElementById('trip_pay_card');
      var bank = document.getElementById('trip_pay_bank');
      var form = document.getElementById('tripPaymentForm');
      function sync() {
        renderTripPaymentsStep();
      }
      if (card) card.addEventListener('change', sync);
      if (bank) bank.addEventListener('change', sync);
      if (form) {
        form.addEventListener('submit', function (e) {
          var tid = sessionStorage.getItem(tripWizardTripIdKey) || '';
          if (!tid) {
            e.preventDefault();
            alert('Submit your trip on Trip Review & Submit first, then return here to pay.');
            return;
          }
          var bankEl = document.getElementById('trip_pay_bank');
          var slip = document.getElementById('trip_bank_transfer_slip');
          if (bankEl && bankEl.checked && slip && (!slip.files || slip.files.length === 0)) {
            e.preventDefault();
            alert('Please upload a screenshot of your bank transfer slip.');
            return;
          }
          if (bankEl && bankEl.checked && slip && slip.files[0] && slip.files[0].size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('File is too large. Maximum size is 5 MB.');
            return;
          }

          // Ensure we return to Review & Submit even if the gateway does not
          // preserve query params on redirect back to customise-trip.
          try { sessionStorage.setItem('ceylonTripWizardReturnToReview', '1'); } catch (e2) {}
        });
      }
    }
    var districtToHotelLocation = { 'colombo': 'colombo', 'kandy': 'kandy', 'galle': 'galle', 'nuwara-eliya': 'nuwara' };
    function filterAccommodationByDestination(leg) {
      leg = leg || '1';
      var destDistrict = '';
      if (leg === '3') destDistrict = ((document.getElementById('dest_district_hidden_3') || {}).value || '');
      else if (leg === '2') destDistrict = ((document.getElementById('dest_district_hidden_2') || {}).value || '');
      else destDistrict = ((document.getElementById('dest_district_hidden') || {}).value || '');
      var prefix = leg === '2' ? 'trip2' : (leg === '3' ? 'trip3' : 'trip');
      var grid = document.getElementById(prefix + 'AccommodationHotelsGrid');
      var locFilter = document.getElementById(prefix + 'AccommodationLocationFilter');
      if (!grid) return;
      var targetLocation = districtToHotelLocation[destDistrict] || destDistrict;
      if (destDistrict && targetLocation) {
        if (locFilter) locFilter.value = targetLocation;
        grid.querySelectorAll('.hotel-card').forEach(function (card) {
          card.style.display = (card.dataset.location || '').indexOf(targetLocation) !== -1 ? '' : 'none';
        });
      } else {
        if (locFilter) locFilter.value = '';
        grid.querySelectorAll('.hotel-card').forEach(function (card) { card.style.display = ''; });
      }
      var searchInput = document.getElementById(prefix + 'AccommodationSearchInput');
      if (searchInput) searchInput.value = '';
    }
    function updateAddStopsButtonLabel() {
      var btn = document.getElementById('btnAddMoreStops');
      var list = document.getElementById('tripStopsList');
      if (!btn || !list) return;
      var n = list.querySelectorAll('.trip-stop-card').length;
      btn.innerHTML = '<i class="fa-solid fa-plus"></i> ' + (n === 0 ? 'Add a stop' : 'Add more stops');
    }
    function updateAddStopsButtonLabel2() {
      var btn = document.getElementById('btnAddMoreStops_2');
      var list = document.getElementById('tripStopsList_2');
      if (!btn || !list) return;
      var n = list.querySelectorAll('.trip-stop-card').length;
      btn.innerHTML = '<i class="fa-solid fa-plus"></i> ' + (n === 0 ? 'Add a stop' : 'Add more stops');
    }
    function allStopLocationsFilled() {
      var list = document.getElementById('tripStopsList');
      if (!list) return false;
      var cards = list.querySelectorAll('.trip-stop-card');
      if (cards.length < 1) return false;
      for (var i = 0; i < cards.length; i++) {
        var locInput = cards[i].querySelector('.trip-stop-location');
        if (!(((locInput && locInput.value) || '').trim())) return false;
      }
      return true;
    }
    function clearTripStopsErrorIfAllLocationsOk() {
      if (!allStopLocationsFilled()) return;
      clearTripStopsStepError();
    }
    function clearTripStopsStepError() {
      var tripStopsErrorEl = document.getElementById('tripStopsError');
      if (tripStopsErrorEl) {
        tripStopsErrorEl.textContent = '';
        tripStopsErrorEl.classList.remove('trip-date-error--visible');
      }
    }
    function validateStep2() {
      var destPrimary = (document.getElementById('dest_primary') || {}).value || '';
      var destDistrict = (document.getElementById('dest_district_hidden') || {}).value || '';
      var tripDateErrorEl = document.getElementById('tripDateError');
      var tripStopsErrorEl = document.getElementById('tripStopsError');
      if (!destPrimary.trim() && !destDistrict.trim()) {
        if (tripStopsErrorEl) {
          tripStopsErrorEl.textContent = '';
          tripStopsErrorEl.classList.remove('trip-date-error--visible');
        }
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'Please select a destination before continuing.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (tripDateErrorEl) {
        tripDateErrorEl.textContent = '';
        tripDateErrorEl.classList.remove('trip-date-error--visible');
      }
      var list = document.getElementById('tripStopsList');
      var stopCount = list ? list.querySelectorAll('.trip-stop-card').length : 0;
      if (stopCount < 1) {
        if (tripStopsErrorEl) {
          tripStopsErrorEl.textContent = 'Please add at least one stop before continuing.';
          tripStopsErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var stopCards = list.querySelectorAll('.trip-stop-card');
      for (var si = 0; si < stopCards.length; si++) {
        var card = stopCards[si];
        var locInput = card.querySelector('.trip-stop-location');
        if (!(((locInput && locInput.value) || '').trim())) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'Please enter the stop location / attraction for Stop ' + (si + 1) + ' (and any other stops) before continuing.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
        var trNo = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="no"]');
        var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
        var gNo = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="no"]');
        var transportChosen = (trYes && trYes.classList.contains('selected')) || (trNo && trNo.classList.contains('selected'));
        var guideChosen = (gYes && gYes.classList.contains('selected')) || (gNo && gNo.classList.contains('selected'));
        if (!transportChosen) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', select Yes or No for Transport needed.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        if (!guideChosen) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', select Yes or No for Tour guide needed.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        if (trYes && trYes.classList.contains('selected')) {
          var pu = ((card.querySelector('.trip-stop-pickup') || {}).value || '').trim();
          var dof = ((card.querySelector('.trip-stop-dropoff') || {}).value || '').trim();
          if (!pu || !dof) {
            if (tripStopsErrorEl) {
              tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', complete transport (pickup and dropoff) in the modal, or choose No for transport.';
              tripStopsErrorEl.classList.add('trip-date-error--visible');
            }
            return false;
          }
        }
        if (gYes && gYes.classList.contains('selected')) {
          var gl = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
          if (!gl) {
            if (tripStopsErrorEl) {
              tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', complete the tour guide request, or choose No for tour guide.';
              tripStopsErrorEl.classList.add('trip-date-error--visible');
            }
            return false;
          }
        }
      }
      if (tripStopsErrorEl) {
        tripStopsErrorEl.textContent = '';
        tripStopsErrorEl.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    function validateStep1() {
      var tripTypeEl = document.getElementById('trip_type');
      var tripTypeErrorEl = document.getElementById('tripTypeError');
      if (!tripTypeEl || !tripTypeEl.value || !tripTypeEl.value.trim()) {
        if (tripTypeErrorEl) {
          tripTypeErrorEl.textContent = 'Please select a type of trip (Couple, Family, Friends, or Solo) before continuing.';
          tripTypeErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var type = tripTypeEl.value.trim().toLowerCase();
      if (type === 'couple') {
        var cAdults = parseInt(document.getElementById('adults').value, 10) || 0;
        var cChildren = parseInt(document.getElementById('children').value, 10) || 0;
        var cInfants = parseInt(document.getElementById('infants').value, 10) || 0;
        if (cAdults !== 2 || cChildren !== 0 || cInfants !== 0) {
          if (tripTypeErrorEl) {
            tripTypeErrorEl.textContent = 'Couple trips require exactly 2 adults (and no children or infants). Use + next to Adults until it shows 2.';
            tripTypeErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
      }
      if (type === 'family' || type === 'friends') {
        var adults = parseInt(document.getElementById('adults').value, 10) || 0;
        var children = parseInt(document.getElementById('children').value, 10) || 0;
        var infants = parseInt(document.getElementById('infants').value, 10) || 0;
        var total = adults + children + infants;
        if (total < 2) {
          if (tripTypeErrorEl) {
            tripTypeErrorEl.textContent = 'Family and Friends trips require at least 2 travelers. Please add more adults, children, or infants.';
            tripTypeErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
      }
      if (tripTypeErrorEl) {
        tripTypeErrorEl.textContent = '';
        tripTypeErrorEl.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    function validateStep4() {
      var v = (document.getElementById('add_another_destination') || {}).value || '';
      var err = document.getElementById('tripAnotherDestError');
      if (v !== 'yes' && v !== 'no') {
        if (err) {
          err.textContent = 'Please choose Yes or No to continue.';
          err.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (err) {
        err.textContent = '';
        err.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    function validateStep5SecondDestination() {
      var destPrimary = (document.getElementById('dest_primary_2') || {}).value || '';
      var destDistrict = (document.getElementById('dest_district_hidden_2') || {}).value || '';
      var tripDateErrorEl = document.getElementById('tripDateError_2');
      var tripStopsErrorEl = document.getElementById('tripStopsError_2');
      if (!destPrimary.trim() && !destDistrict.trim()) {
        if (tripStopsErrorEl) {
          tripStopsErrorEl.textContent = '';
          tripStopsErrorEl.classList.remove('trip-date-error--visible');
        }
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'Please select a destination before continuing.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (tripDateErrorEl) {
        tripDateErrorEl.textContent = '';
        tripDateErrorEl.classList.remove('trip-date-error--visible');
      }
      var sd = document.getElementById('start_date_2');
      var ed = document.getElementById('end_date_2');
      if (!sd || !ed || !sd.value || !ed.value) {
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'Please choose start and end dates for this destination.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (new Date(ed.value) < new Date(sd.value)) {
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'End date must be on or after start date.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var list = document.getElementById('tripStopsList_2');
      var stopCount = list ? list.querySelectorAll('.trip-stop-card').length : 0;
      if (stopCount < 1) {
        if (tripStopsErrorEl) {
          tripStopsErrorEl.textContent = 'Please add at least one stop before continuing.';
          tripStopsErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var stopCards = list.querySelectorAll('.trip-stop-card');
      for (var si = 0; si < stopCards.length; si++) {
        var card = stopCards[si];
        var locInput = card.querySelector('.trip-stop-location');
        if (!(((locInput && locInput.value) || '').trim())) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'Please enter the stop location / attraction for Stop ' + (si + 1) + ' (and any other stops) before continuing.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
        var trNo = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="no"]');
        var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
        var gNo = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="no"]');
        var transportChosen = (trYes && trYes.classList.contains('selected')) || (trNo && trNo.classList.contains('selected'));
        var guideChosen = (gYes && gYes.classList.contains('selected')) || (gNo && gNo.classList.contains('selected'));
        if (!transportChosen) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', select Yes or No for Transport needed.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        if (!guideChosen) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', select Yes or No for Tour guide needed.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        if (trYes && trYes.classList.contains('selected')) {
          var pu = ((card.querySelector('.trip-stop-pickup') || {}).value || '').trim();
          var dof = ((card.querySelector('.trip-stop-dropoff') || {}).value || '').trim();
          if (!pu || !dof) {
            if (tripStopsErrorEl) {
              tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', complete transport (pickup and dropoff) in the modal, or choose No for transport.';
              tripStopsErrorEl.classList.add('trip-date-error--visible');
            }
            return false;
          }
        }
        if (gYes && gYes.classList.contains('selected')) {
          var gl = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
          if (!gl) {
            if (tripStopsErrorEl) {
              tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', complete the tour guide request, or choose No for tour guide.';
              tripStopsErrorEl.classList.add('trip-date-error--visible');
            }
            return false;
          }
        }
      }
      if (tripStopsErrorEl) {
        tripStopsErrorEl.textContent = '';
        tripStopsErrorEl.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    function validateStep7ThirdChoice() {
      var v = (document.getElementById('add_third_destination') || {}).value || '';
      var err = document.getElementById('tripThirdDestError');
      if (v !== 'yes' && v !== 'no') {
        if (err) {
          err.textContent = 'Please choose Yes or No to continue.';
          err.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (err) {
        err.textContent = '';
        err.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    function validateStep8ThirdDestination() {
      var destPrimary = (document.getElementById('dest_primary_3') || {}).value || '';
      var destDistrict = (document.getElementById('dest_district_hidden_3') || {}).value || '';
      var tripDateErrorEl = document.getElementById('tripDateError_3');
      var tripStopsErrorEl = document.getElementById('tripStopsError_3');
      if (!destPrimary.trim() && !destDistrict.trim()) {
        if (tripStopsErrorEl) {
          tripStopsErrorEl.textContent = '';
          tripStopsErrorEl.classList.remove('trip-date-error--visible');
        }
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'Please select a destination before continuing.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (tripDateErrorEl) {
        tripDateErrorEl.textContent = '';
        tripDateErrorEl.classList.remove('trip-date-error--visible');
      }
      var sd = document.getElementById('start_date_3');
      var ed = document.getElementById('end_date_3');
      if (!sd || !ed || !sd.value || !ed.value) {
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'Please choose start and end dates for this destination.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (new Date(ed.value) < new Date(sd.value)) {
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'End date must be on or after start date.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var list = document.getElementById('tripStopsList_3');
      var stopCount = list ? list.querySelectorAll('.trip-stop-card').length : 0;
      if (stopCount < 1) {
        if (tripStopsErrorEl) {
          tripStopsErrorEl.textContent = 'Please add at least one stop before continuing.';
          tripStopsErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var stopCards = list.querySelectorAll('.trip-stop-card');
      for (var si = 0; si < stopCards.length; si++) {
        var card = stopCards[si];
        var locInput = card.querySelector('.trip-stop-location');
        if (!(((locInput && locInput.value) || '').trim())) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'Please enter the stop location / attraction for Stop ' + (si + 1) + ' (and any other stops) before continuing.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        var trYes = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
        var trNo = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="no"]');
        var gYes = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
        var gNo = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="no"]');
        var transportChosen = (trYes && trYes.classList.contains('selected')) || (trNo && trNo.classList.contains('selected'));
        var guideChosen = (gYes && gYes.classList.contains('selected')) || (gNo && gNo.classList.contains('selected'));
        if (!transportChosen) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', select Yes or No for Transport needed.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        if (!guideChosen) {
          if (tripStopsErrorEl) {
            tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', select Yes or No for Tour guide needed.';
            tripStopsErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
        if (trYes && trYes.classList.contains('selected')) {
          var pu = ((card.querySelector('.trip-stop-pickup') || {}).value || '').trim();
          var dof = ((card.querySelector('.trip-stop-dropoff') || {}).value || '').trim();
          if (!pu || !dof) {
            if (tripStopsErrorEl) {
              tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', complete transport (pickup and dropoff) in the modal, or choose No for transport.';
              tripStopsErrorEl.classList.add('trip-date-error--visible');
            }
            return false;
          }
        }
        if (gYes && gYes.classList.contains('selected')) {
          var gl = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
          if (!gl) {
            if (tripStopsErrorEl) {
              tripStopsErrorEl.textContent = 'For Stop ' + (si + 1) + ', complete the tour guide request, or choose No for tour guide.';
              tripStopsErrorEl.classList.add('trip-date-error--visible');
            }
            return false;
          }
        }
      }
      if (tripStopsErrorEl) {
        tripStopsErrorEl.textContent = '';
        tripStopsErrorEl.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    if (btnNext) btnNext.addEventListener('click', function () {
      if (currentStep === 1 && !validateStep1()) return;
      if (currentStep === 2 && !validateStep2()) return;
      if (currentStep === 4 && !validateStep4()) return;
      if (currentStep === 5 && !validateStep5SecondDestination()) return;
      if (currentStep === 7 && !validateStep7ThirdChoice()) return;
      if (currentStep === 8 && !validateStep8ThirdDestination()) return;
      // Do not allow proceeding to Verify bookings until the trip is actually submitted (not only a server trip id).
      if (currentStep === 11) {
        try { syncTripWizardSubmissionState(); } catch (e) {}
        var submittedOk = false;
        try { submittedOk = sessionStorage.getItem(tripWizardSubmittedKey) === '1'; } catch (eS) {}
        var tid = '';
        try { tid = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (e) {}
        if (!submittedOk || !tid) {
          alert('Please submit your trip first (use the Submit trip button on this page).');
          return;
        }
      }
      // Verify bookings: nothing pending — must be approved before Payments.
      if (currentStep === 12) {
        var gateStep = validateVerifyBookingsApproved();
        if (!gateStep.ok) {
          alert(gateStep.message);
          return;
        }
        var tid2 = '';
        var proceeded = '';
        try { tid2 = sessionStorage.getItem(tripWizardTripIdKey) || ''; } catch (e) {}
        try { proceeded = sessionStorage.getItem(tripWizardProceedKey) || ''; } catch (e) {}
        if (!tid2) {
          alert('Please submit your trip first.');
          showStep(11);
          return;
        }
        if (proceeded !== String(tid2)) {
          alert('Please click "Proceed to Payment" to continue.');
          return;
        }
      }
      // Payments: require recorded payment (same check as Trip Overview) before Trip Overview.
      if (currentStep === 13) {
        var tidPay = '';
        try { tidPay = String(sessionStorage.getItem(tripWizardTripIdKey) || '').trim(); } catch (ePay) { tidPay = ''; }
        if (!tidPay && typeof serverLastTid !== 'undefined' && serverLastTid > 0) {
          tidPay = String(serverLastTid);
        }
        if (!tidPay) {
          alert('Submit your trip on Trip Review & Submit first so you have a trip reference to pay.');
          return;
        }
        var nextBtn = btnNext;
        var prevHtml = nextBtn ? nextBtn.innerHTML : '';
        if (nextBtn) {
          nextBtn.disabled = true;
          nextBtn.innerHTML = 'Checking…';
        }
        fetch('/CeylonGo/public/tourist/trip-payment-status/' + encodeURIComponent(tidPay), {
          method: 'GET',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        })
          .then(function (r) {
            return r.text().then(function (text) {
              var data = null;
              try { data = JSON.parse(text); } catch (eJ) { data = null; }
              return { ok: r.ok, status: r.status, data: data };
            });
          })
          .then(function (res) {
            if (nextBtn) {
              nextBtn.disabled = false;
              nextBtn.innerHTML = prevHtml;
            }
            var data = res.data;
            if (!data || !data.success || !data.trip) {
              var errMsg = (data && data.error) ? data.error : 'Could not verify payment status.';
              if (!res.ok && res.status === 401) {
                errMsg = 'Your session expired. Please sign in again.';
              }
              alert(errMsg);
              return;
            }
            if (!deriveTripPaymentFlags(data.trip).showOverview) {
              alert('Complete your payment first. For card, finish secure checkout. For bank transfer, upload your slip and click Continue — then tap Next.');
              return;
            }
            var next = computeNextStep(13);
            if (next !== 13) showStep(next);
          })
          .catch(function () {
            if (nextBtn) {
              nextBtn.disabled = false;
              nextBtn.innerHTML = prevHtml;
            }
            alert('Could not verify payment status. Check your connection and try again.');
          });
        return;
      }
      var next = computeNextStep(currentStep);
      if (next !== currentStep) showStep(next);
    });
    if (btnPrev) btnPrev.addEventListener('click', function () {
      if (currentStep > 1) showStep(computePrevStep(currentStep));
    });
    // Initial step is applied after stop lists + draft restore (see tripWizardBootstrapAfterStops).
    (function () {
      tripWizardPendingInitialStep = 1;
      tripWizardUrlForcedStep = false;
      try {
        if (tripPageWizardFresh) {
          tripWizardPendingInitialStep = 1;
          tripWizardUrlForcedStep = true;
          return;
        }
        var fromPay = '';
        try { fromPay = String(sessionStorage.getItem('ceylonTripWizardReturnToReview') || ''); } catch (e0) { fromPay = ''; }
        if (fromPay === '1') {
          tripWizardPendingInitialStep = 14;
          tripWizardUrlForcedStep = true;
          try { sessionStorage.removeItem('ceylonTripWizardReturnToReview'); } catch (e1) {}
        }
        var qs = new URLSearchParams(window.location.search || '');
        if (qs.get('afterPayment') === '1') {
          tripWizardPendingInitialStep = 14;
          tripWizardUrlForcedStep = true;
        }
        var rawStep = qs.get('step');
        if (rawStep) {
          var s = parseInt(rawStep, 10);
          if (!isNaN(s) && s >= 1 && s <= totalSteps) {
            tripWizardPendingInitialStep = clampWizardStepToAllowed(s);
            tripWizardUrlForcedStep = true;
          }
        }
      } catch (e) {}
    })();
    updateAddStopsButtonLabel();

    (function initAnotherDestinationChoice() {
      var panel = document.querySelector('.trip-step-panel[data-step="4"]');
      if (!panel) return;
      var cards = panel.querySelectorAll('.trip-another-dest-card');
      var hid = document.getElementById('add_another_destination');
      var err = document.getElementById('tripAnotherDestError');
      if (!cards.length || !hid) return;
      cards.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var val = this.getAttribute('data-value') || '';
          cards.forEach(function (b) {
            b.classList.remove('selected');
            b.setAttribute('aria-pressed', 'false');
          });
          this.classList.add('selected');
          this.setAttribute('aria-pressed', 'true');
          hid.value = val;
          if (val === 'no') {
            secondStopsInitialized = false;
            thirdStopsInitialized = false;
            var hid3 = document.getElementById('add_third_destination');
            if (hid3) hid3.value = '';
          }
          updateLegStepperVisibility();
          if (err) {
            err.textContent = '';
            err.classList.remove('trip-date-error--visible');
          }
          if (currentStep === 4 && (val === 'yes' || val === 'no')) {
            showStep(computeNextStep(4));
          }
        });
      });
    })();

    (function initThirdDestinationChoice() {
      var panel = document.querySelector('.trip-step-panel[data-step="7"]');
      if (!panel) return;
      var cards = panel.querySelectorAll('.trip-another-dest-card');
      var hid = document.getElementById('add_third_destination');
      var err = document.getElementById('tripThirdDestError');
      if (!cards.length || !hid) return;
      cards.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var val = this.getAttribute('data-value') || '';
          cards.forEach(function (b) {
            b.classList.remove('selected');
            b.setAttribute('aria-pressed', 'false');
          });
          this.classList.add('selected');
          this.setAttribute('aria-pressed', 'true');
          hid.value = val;
          if (val === 'no') thirdStopsInitialized = false;
          updateLegStepperVisibility();
          if (err) {
            err.textContent = '';
            err.classList.remove('trip-date-error--visible');
          }
          if (currentStep === 7 && (val === 'yes' || val === 'no')) {
            showStep(computeNextStep(7));
          }
        });
      });
    })();

    document.querySelectorAll('.trip-counter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var targetId = this.getAttribute('data-target');
        var dir = parseInt(this.getAttribute('data-dir'), 10);
        var input = document.getElementById(targetId);
        if (!input) return;
        var min = parseInt(input.getAttribute('min'), 10), max = parseInt(input.getAttribute('max'), 10);
        var val = parseInt(input.value, 10) + dir;
        input.value = Math.max(min, Math.min(max, val));
      });
    });

    var adultsInput = document.getElementById('adults');
    var childrenInput = document.getElementById('children');
    var infantsInput = document.getElementById('infants');
    var tripUserName = <?php echo json_encode($user_name); ?>;
    var tripUserEmail = <?php echo json_encode($user_email); ?>;
    var tripUserContact = <?php echo json_encode((is_array($tourist_data) && isset($tourist_data['contact_number'])) ? $tourist_data['contact_number'] : ''); ?>;
    function applyAdultLimitForTripType(type) {
      if (!adultsInput) return;
      if (type === 'solo') {
        adultsInput.setAttribute('min', '1');
        adultsInput.setAttribute('max', '1'); adultsInput.value = '1';
        if (childrenInput) { childrenInput.setAttribute('max', '0'); childrenInput.value = '0'; }
        if (infantsInput) { infantsInput.setAttribute('max', '0'); infantsInput.value = '0'; }
      } else if (type === 'couple') {
        adultsInput.setAttribute('min', '2');
        adultsInput.setAttribute('max', '2');
        adultsInput.value = '2';
        if (childrenInput) { childrenInput.setAttribute('max', '0'); childrenInput.value = '0'; }
        if (infantsInput) { infantsInput.setAttribute('max', '0'); infantsInput.value = '0'; }
      } else {
        adultsInput.setAttribute('min', '1');
        adultsInput.setAttribute('max', '50');
        if (childrenInput) childrenInput.setAttribute('max', '50');
        if (infantsInput) infantsInput.setAttribute('max', '50');
      }
    }
    document.querySelectorAll('.trip-type-card').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.trip-type-card').forEach(function (b) {
          b.classList.remove('selected');
          var icon = b.querySelector('.trip-type-card-icon');
          if (icon) { icon.className = 'trip-type-card-icon fa-regular fa-heart'; }
        });
        this.classList.add('selected');
        var icon = this.querySelector('.trip-type-card-icon');
        if (icon) { icon.className = 'trip-type-card-icon fa-solid fa-heart'; }
        var type = this.getAttribute('data-type');
        var hid = document.getElementById('trip_type');
        if (hid) hid.value = type;
        applyAdultLimitForTripType(type);
        var tripTypeErrorEl = document.getElementById('tripTypeError');
        if (tripTypeErrorEl) { tripTypeErrorEl.textContent = ''; tripTypeErrorEl.classList.remove('trip-date-error--visible'); }
      });
    });

    var durationEl = document.getElementById('tripDuration');
    var startDateEl = document.getElementById('start_date');
    var endDateEl = document.getElementById('end_date');
    var dateErrorEl = document.getElementById('tripDateError');

    function todayStr() {
      var d = new Date();
      return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    function minStartDateStr() {
      var d = new Date();
      d.setDate(d.getDate() + 21);
      return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function validateDates() {
      if (!dateErrorEl) return true;
      dateErrorEl.textContent = '';
      dateErrorEl.classList.remove('trip-date-error--visible');
      if (!startDateEl || !endDateEl) return true;
      var startVal = startDateEl.value;
      var endVal = endDateEl.value;
      if (!startVal || !endVal) return true;
      var start = new Date(startVal);
      var end = new Date(endVal);
      if (end < start) {
        dateErrorEl.textContent = 'End date must be on or after start date.';
        dateErrorEl.classList.add('trip-date-error--visible');
        return false;
      }
      return true;
    }

    function updateDurationBanner() {
      if (!durationEl || !startDateEl || !endDateEl) return;
      if (!validateDates()) {
        durationEl.textContent = '—';
        return;
      }
      var start = startDateEl.value ? new Date(startDateEl.value) : null;
      var end = endDateEl.value ? new Date(endDateEl.value) : null;
      if (!start || !end || end < start) {
        durationEl.textContent = '—';
        return;
      }
      var nights = Math.round((end - start) / (24 * 60 * 60 * 1000));
      if (nights < 0) nights = 0;
      durationEl.textContent = nights + ' Night' + (nights !== 1 ? 's' : '');
    }

    if (startDateEl) {
      var minStart = minStartDateStr();
      startDateEl.setAttribute('min', minStart);
      if (startDateEl.value && startDateEl.value < minStart) {
        startDateEl.value = minStart;
        if (endDateEl && endDateEl.value && endDateEl.value < minStart) endDateEl.value = minStart;
      }
      startDateEl.addEventListener('change', function () {
        if (endDateEl && startDateEl.value) endDateEl.setAttribute('min', startDateEl.value);
        if (endDateEl && endDateEl.value && endDateEl.value < startDateEl.value) endDateEl.value = startDateEl.value;
        updateDurationBanner();
      });
    }
    if (endDateEl) {
      if (startDateEl && startDateEl.value) endDateEl.setAttribute('min', startDateEl.value);
      endDateEl.addEventListener('change', updateDurationBanner);
    }
    updateDurationBanner();

    var durationEl2 = document.getElementById('tripDuration_2');
    var startDateEl2 = document.getElementById('start_date_2');
    var endDateEl2 = document.getElementById('end_date_2');
    var dateErrorEl2 = document.getElementById('tripDateError_2');
    function validateDates2() {
      if (!dateErrorEl2) return true;
      dateErrorEl2.textContent = '';
      dateErrorEl2.classList.remove('trip-date-error--visible');
      if (!startDateEl2 || !endDateEl2) return true;
      var startVal = startDateEl2.value;
      var endVal = endDateEl2.value;
      if (!startVal || !endVal) return true;
      var start = new Date(startVal);
      var end = new Date(endVal);
      if (end < start) {
        dateErrorEl2.textContent = 'End date must be on or after start date.';
        dateErrorEl2.classList.add('trip-date-error--visible');
        return false;
      }
      return true;
    }
    function updateDurationBanner2() {
      if (!durationEl2 || !startDateEl2 || !endDateEl2) return;
      if (!validateDates2()) {
        durationEl2.textContent = '—';
        return;
      }
      var start = startDateEl2.value ? new Date(startDateEl2.value) : null;
      var end = endDateEl2.value ? new Date(endDateEl2.value) : null;
      if (!start || !end || end < start) {
        durationEl2.textContent = '—';
        return;
      }
      var nights = Math.round((end - start) / (24 * 60 * 60 * 1000));
      if (nights < 0) nights = 0;
      durationEl2.textContent = nights + ' Night' + (nights !== 1 ? 's' : '');
    }
    function syncSecondDestinationDefaultDates() {
      var end1 = document.getElementById('end_date');
      var start2 = document.getElementById('start_date_2');
      var end2 = document.getElementById('end_date_2');
      var ms = minStartDateStr();
      var minStart2 = ms;
      if (end1 && end1.value) {
        var d = new Date(end1.value + 'T12:00:00');
        d.setDate(d.getDate() + 1);
        var y = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        if (y > minStart2) minStart2 = y;
        if (start2 && !start2.value) start2.value = y;
      }
      if (start2) {
        start2.setAttribute('min', minStart2);
        if (start2.value && start2.value < minStart2) start2.value = minStart2;
      }
      if (start2 && start2.value && end2) {
        end2.setAttribute('min', start2.value);
        if (!end2.value || end2.value < start2.value) end2.value = start2.value;
      }
      updateDurationBanner2();
    }
    if (startDateEl2) {
      startDateEl2.addEventListener('change', function () {
        if (endDateEl2 && startDateEl2.value) endDateEl2.setAttribute('min', startDateEl2.value);
        if (endDateEl2 && endDateEl2.value && endDateEl2.value < startDateEl2.value) endDateEl2.value = startDateEl2.value;
        updateDurationBanner2();
      });
    }
    if (endDateEl2) {
      endDateEl2.addEventListener('change', updateDurationBanner2);
    }

    var durationEl3 = document.getElementById('tripDuration_3');
    var startDateEl3 = document.getElementById('start_date_3');
    var endDateEl3 = document.getElementById('end_date_3');
    var dateErrorEl3 = document.getElementById('tripDateError_3');
    function validateDates3() {
      if (!dateErrorEl3) return true;
      dateErrorEl3.textContent = '';
      dateErrorEl3.classList.remove('trip-date-error--visible');
      if (!startDateEl3 || !endDateEl3) return true;
      var startVal = startDateEl3.value;
      var endVal = endDateEl3.value;
      if (!startVal || !endVal) return true;
      var start = new Date(startVal);
      var end = new Date(endVal);
      if (end < start) {
        dateErrorEl3.textContent = 'End date must be on or after start date.';
        dateErrorEl3.classList.add('trip-date-error--visible');
        return false;
      }
      return true;
    }
    function updateDurationBanner3() {
      if (!durationEl3 || !startDateEl3 || !endDateEl3) return;
      if (!validateDates3()) {
        durationEl3.textContent = '—';
        return;
      }
      var start = startDateEl3.value ? new Date(startDateEl3.value) : null;
      var end = endDateEl3.value ? new Date(endDateEl3.value) : null;
      if (!start || !end || end < start) {
        durationEl3.textContent = '—';
        return;
      }
      var nights = Math.round((end - start) / (24 * 60 * 60 * 1000));
      if (nights < 0) nights = 0;
      durationEl3.textContent = nights + ' Night' + (nights !== 1 ? 's' : '');
    }
    function syncThirdDestinationDefaultDates() {
      var end2 = document.getElementById('end_date_2');
      var start3 = document.getElementById('start_date_3');
      var end3 = document.getElementById('end_date_3');
      var ms = minStartDateStr();
      var minStart3 = ms;
      if (end2 && end2.value) {
        var d = new Date(end2.value + 'T12:00:00');
        d.setDate(d.getDate() + 1);
        var y = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        if (y > minStart3) minStart3 = y;
        if (start3 && !start3.value) start3.value = y;
      }
      if (start3) {
        start3.setAttribute('min', minStart3);
        if (start3.value && start3.value < minStart3) start3.value = minStart3;
      }
      if (start3 && start3.value && end3) {
        end3.setAttribute('min', start3.value);
        if (!end3.value || end3.value < start3.value) end3.value = start3.value;
      }
      updateDurationBanner3();
    }
    if (startDateEl3) {
      startDateEl3.addEventListener('change', function () {
        if (endDateEl3 && startDateEl3.value) endDateEl3.setAttribute('min', startDateEl3.value);
        if (endDateEl3 && endDateEl3.value && endDateEl3.value < startDateEl3.value) endDateEl3.value = startDateEl3.value;
        updateDurationBanner3();
      });
    }
    if (endDateEl3) {
      endDateEl3.addEventListener('change', updateDurationBanner3);
    }

    var stopIndex = 0;
    var stopIndex2 = 0;
    var stopIndex3 = 0;
    var tripStopCardForTransport = null;
    var tripStopCardForGuide = null;
    function escTripAttr(s) {
      return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }
    function clearTransportStopData(card) {
      if (!card) return;
      var pu = card.querySelector('.trip-stop-pickup');
      var dof = card.querySelector('.trip-stop-dropoff');
      var fare = card.querySelector('.trip-stop-fare-amount');
      if (pu) { pu.value = ''; delete pu.dataset.placeId; }
      if (dof) { dof.value = ''; delete dof.dataset.placeId; }
      if (fare) fare.value = '';
      ['.trip-stop-tr-date', '.trip-stop-tr-vehicle', '.trip-stop-tr-time', '.trip-stop-tr-people'].forEach(function (sel) {
        var el = card.querySelector(sel);
        if (el) el.value = '';
      });
    }
    function clearGuideStopData(card) {
      if (!card) return;
      var gl = card.querySelector('.trip-stop-guide-location');
      if (gl) gl.value = '';
      ['.trip-stop-guide-date', '.trip-stop-guide-language', '.trip-stop-guide-time', '.trip-stop-guide-notes'].forEach(function (sel) {
        var el = card.querySelector(sel);
        if (el) el.value = '';
      });
    }
    function syncStopCardFields(card) {
      if (!card) return;
      var trFields = card.querySelector('.trip-stop-transport-yes-fields');
      var gFields = card.querySelector('.trip-stop-guide-yes-fields');
      if (trFields) trFields.style.display = 'none';
      if (gFields) gFields.style.display = 'none';
    }
    function updateStopCardSummary(card) {
      if (!card) return;
      var loc = ((card.querySelector('.trip-stop-location') || {}).value || '').trim();
      var trYesBtn = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="yes"]');
      var trNoBtn = card.querySelector('.trip-stop-opt-transport .trip-toggle-btn[data-value="no"]');
      var gYesBtn = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="yes"]');
      var gNoBtn = card.querySelector('.trip-stop-opt-guide .trip-toggle-btn[data-value="no"]');
      var transportYes = trYesBtn && trYesBtn.classList.contains('selected');
      var transportNo = trNoBtn && trNoBtn.classList.contains('selected');
      var guideYes = gYesBtn && gYesBtn.classList.contains('selected');
      var guideNo = gNoBtn && gNoBtn.classList.contains('selected');
      var lines = [];
      lines.push('<p><strong>Stop:</strong> ' + escTripAttr(loc) + '</p>');
      if (transportYes) {
        var pu = ((card.querySelector('.trip-stop-pickup') || {}).value || '').trim();
        var dropoff = ((card.querySelector('.trip-stop-dropoff') || {}).value || '').trim();
        var fareNum = ((card.querySelector('.trip-stop-fare-amount') || {}).value || '').trim();
        var innerParts = [];
        if (pu) innerParts.push('<span class="trip-stop-summary-sublabel">Pickup:</span> ' + escTripAttr(pu));
        if (dropoff) innerParts.push('<span class="trip-stop-summary-sublabel">Dropoff:</span> ' + escTripAttr(dropoff));
        if (fareNum) innerParts.push('<span class="trip-stop-summary-sublabel">Fare:</span> LKR ' + escTripAttr(Number(fareNum).toFixed(2)));
        var inner = innerParts.join('<br>');
        lines.push('<p class="trip-stop-summary-transport"><strong>Transport:</strong>' + (inner ? '<br>' + inner : '') + '</p>');
      } else if (transportNo) {
        lines.push('<p><strong>Transport:</strong> ' + escTripAttr('No') + '</p>');
      } else {
        lines.push('<p><strong>Transport:</strong></p>');
      }
      var guideLine;
      if (guideYes) {
        guideLine = ((card.querySelector('.trip-stop-guide-location') || {}).value || '').trim();
      } else if (guideNo) {
        guideLine = 'No';
      } else {
        guideLine = '';
      }
      lines.push('<p><strong>Tour Guide:</strong>' + (guideLine ? ' ' + escTripAttr(guideLine) : '') + '</p>');
      var body = card.querySelector('.trip-stop-summary-body');
      if (body) body.innerHTML = lines.join('');
    }
    function resetTransportModalFieldsForStop() {
      var puEl = document.getElementById('tr_pickupLocation');
      var dofEl = document.getElementById('tr_dropoffLocation');
      if (puEl) {
        puEl.value = '';
        delete puEl.dataset.placeId;
      }
      if (dofEl) {
        dofEl.value = '';
        delete dofEl.dataset.placeId;
      }
      var vehicleSelect = document.getElementById('tr_vehicleType');
      if (vehicleSelect) vehicleSelect.value = '';
      var estimatedFare = document.getElementById('tr_estimatedFare');
      var fareValEl = document.getElementById('tr_estimatedFareValue');
      var distValEl = document.getElementById('tr_distanceValue');
      if (estimatedFare) estimatedFare.value = 'LKR 0.00';
      if (fareValEl) fareValEl.value = '';
      if (distValEl) distValEl.value = '';
      var breakdown = document.getElementById('tr_fareBreakdown');
      if (breakdown) {
        breakdown.style.display = 'none';
        var distD = document.getElementById('tr_fareDistance');
        var rateD = document.getElementById('tr_fareBaseRate');
        var totalD = document.getElementById('tr_fareTotal');
        if (distD) distD.textContent = '—';
        if (rateD) rateD.textContent = '—';
        if (totalD) totalD.textContent = '—';
      }
      var errEl = document.getElementById('tr_vehicleError');
      if (errEl) {
        errEl.style.display = 'none';
        errEl.textContent = '';
      }
      var confirmBtn = document.getElementById('tr_btnConfirm');
      if (confirmBtn) confirmBtn.disabled = true;
      var notes = document.getElementById('tr_notes');
      if (notes) notes.value = '';
      var pt = document.getElementById('tr_pickupTime');
      if (pt) pt.value = '';
    }
    function isStopCardInLeg2(card) {
      return !!(card && card.closest && card.closest('#tripStopsList_2'));
    }
    function isStopCardInLeg3(card) {
      return !!(card && card.closest && card.closest('#tripStopsList_3'));
    }
    function openTransportModalForStop(card) {
      var overlay = document.getElementById('transportRequestModalOverlay');
      if (!overlay || !card) return;
      tripStopCardForTransport = card;
      resetTransportModalFieldsForStop();
      var puEl = document.getElementById('tr_pickupLocation');
      var dofEl = document.getElementById('tr_dropoffLocation');
      var puH = card.querySelector('.trip-stop-pickup');
      var dofH = card.querySelector('.trip-stop-dropoff');
      var stopLocInput = card.querySelector('.trip-stop-location');
      var stopLoc = stopLocInput ? (stopLocInput.value || '').trim() : '';
      if (puEl && puH) {
        puEl.value = puH.value || '';
        if (puH.dataset && puH.dataset.placeId) puEl.dataset.placeId = puH.dataset.placeId; else delete puEl.dataset.placeId;
      }
      if (dofEl && dofH) {
        dofEl.value = dofH.value || '';
        if (dofH.dataset && dofH.dataset.placeId) dofEl.dataset.placeId = dofH.dataset.placeId; else delete dofEl.dataset.placeId;
      }
      // Prefill dropoff with the stop location (if dropoff is empty).
      if (dofEl && (!dofEl.value || !String(dofEl.value).trim()) && stopLoc) {
        dofEl.value = stopLoc;
        if (stopLocInput && stopLocInput.dataset && stopLocInput.dataset.placeId) {
          dofEl.dataset.placeId = stopLocInput.dataset.placeId;
        } else if (dofEl.dataset) {
          delete dofEl.dataset.placeId;
        }
      }
      var leg3 = isStopCardInLeg3(card);
      var leg2 = isStopCardInLeg2(card);
      var startEl = document.getElementById(leg3 ? 'start_date_3' : (leg2 ? 'start_date_2' : 'start_date'));
      var endEl = document.getElementById(leg3 ? 'end_date_3' : (leg2 ? 'end_date_2' : 'end_date'));
      var trDateEl = document.getElementById('tr_date');
      if (trDateEl && startEl && endEl && startEl.value && endEl.value) {
        trDateEl.setAttribute('min', startEl.value);
        trDateEl.setAttribute('max', endEl.value);
        var v = trDateEl.value;
        if (v && (v < startEl.value || v > endEl.value)) trDateEl.value = '';
      }
      var trNumPeopleEl = document.getElementById('tr_numPeople');
      var adultsEl = document.getElementById('adults');
      var childrenEl = document.getElementById('children');
      var infantsEl = document.getElementById('infants');
      if (trNumPeopleEl && adultsEl && childrenEl && infantsEl) {
        var total = (parseInt(adultsEl.value, 10) || 0) + (parseInt(childrenEl.value, 10) || 0) + (parseInt(infantsEl.value, 10) || 0);
        trNumPeopleEl.value = Math.max(1, total);
      }
      var tdH = card.querySelector('.trip-stop-tr-date');
      var tvH = card.querySelector('.trip-stop-tr-vehicle');
      var ttH = card.querySelector('.trip-stop-tr-time');
      var tpH = card.querySelector('.trip-stop-tr-people');
      if (trDateEl && tdH && tdH.value) trDateEl.value = tdH.value;
      var vehicleSelect = document.getElementById('tr_vehicleType');
      if (vehicleSelect && tvH && tvH.value) vehicleSelect.value = tvH.value;
      var ptEl = document.getElementById('tr_pickupTime');
      if (ptEl && ttH && ttH.value) ptEl.value = ttH.value;
      if (trNumPeopleEl && tpH && tpH.value) trNumPeopleEl.value = tpH.value;
      if (typeof showTransportForm === 'function') showTransportForm();
      overlay.classList.add('trip-modal-open');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function openTourGuideModalForStop(card) {
      var overlay = document.getElementById('tourGuideRequestModalOverlay');
      if (!overlay || !card) return;
      tripStopCardForGuide = card;
      var stopLocInput = card.querySelector('.trip-stop-location');
      var stopLoc = stopLocInput ? (stopLocInput.value || '').trim() : '';
      var locH = card.querySelector('.trip-stop-guide-location');
      var guideLoc = locH ? (locH.value || '').trim() : '';
      var tgLoc = document.getElementById('tg_location');
      if (tgLoc) {
        tgLoc.value = stopLoc || guideLoc || '';
        if (stopLocInput && stopLocInput.dataset && stopLocInput.dataset.placeId) {
          tgLoc.dataset.placeId = stopLocInput.dataset.placeId;
        } else if (tgLoc.dataset) {
          delete tgLoc.dataset.placeId;
        }
      }
      var leg3 = isStopCardInLeg3(card);
      var leg2 = isStopCardInLeg2(card);
      var startEl = document.getElementById(leg3 ? 'start_date_3' : (leg2 ? 'start_date_2' : 'start_date'));
      var endEl = document.getElementById(leg3 ? 'end_date_3' : (leg2 ? 'end_date_2' : 'end_date'));
      var tgDate = document.getElementById('tg_date');
      if (tgDate && startEl && startEl.value) {
        tgDate.setAttribute('min', startEl.value);
        if (endEl && endEl.value) tgDate.setAttribute('max', endEl.value);
      }
      var gdh = card.querySelector('.trip-stop-guide-date');
      var glh = card.querySelector('.trip-stop-guide-language');
      var gth = card.querySelector('.trip-stop-guide-time');
      var gnh = card.querySelector('.trip-stop-guide-notes');
      if (tgDate && gdh && gdh.value) tgDate.value = gdh.value;
      var tgLang = document.getElementById('tg_language');
      if (tgLang && glh && glh.value) tgLang.value = glh.value;
      var tgTime = document.getElementById('tg_time');
      if (tgTime && gth && gth.value) tgTime.value = gth.value;
      var tgNotes = document.getElementById('tg_notes');
      if (tgNotes && gnh) tgNotes.value = gnh.value || '';
      overlay.classList.add('trip-modal-open');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function applyTransportModalToStopCard() {
      var card = tripStopCardForTransport;
      if (!card) return;
      var puH = card.querySelector('.trip-stop-pickup');
      var dofH = card.querySelector('.trip-stop-dropoff');
      var fareH = card.querySelector('.trip-stop-fare-amount');
      var puEl = document.getElementById('tr_pickupLocation');
      var dofEl = document.getElementById('tr_dropoffLocation');
      var fareVal = document.getElementById('tr_estimatedFareValue');
      if (puH && puEl) {
        puH.value = (puEl.value || '').trim();
        if (puEl.dataset && puEl.dataset.placeId) puH.dataset.placeId = puEl.dataset.placeId; else delete puH.dataset.placeId;
      }
      if (dofH && dofEl) {
        dofH.value = (dofEl.value || '').trim();
        if (dofEl.dataset && dofEl.dataset.placeId) dofH.dataset.placeId = dofEl.dataset.placeId; else delete dofH.dataset.placeId;
      }
      if (fareH && fareVal) fareH.value = (fareVal.value || '').trim();
      function setStopHid(sel, srcId) {
        var h = card.querySelector(sel);
        var src = document.getElementById(srcId);
        if (h && src) h.value = (src.value || '').trim();
      }
      setStopHid('.trip-stop-tr-date', 'tr_date');
      setStopHid('.trip-stop-tr-vehicle', 'tr_vehicleType');
      setStopHid('.trip-stop-tr-time', 'tr_pickupTime');
      setStopHid('.trip-stop-tr-people', 'tr_numPeople');
      updateStopCardSummary(card);
      if (isStopCardInLeg3(card)) clearTripStopsStepError3();
      else if (isStopCardInLeg2(card)) clearTripStopsStepError2();
      else clearTripStopsStepError();
      tripStopCardForTransport = null;
      if (typeof closeTransportModal === 'function') closeTransportModal();
    }
    function addStopCard(data, opts) {
      opts = opts || {};
      var listId = opts.listId || 'tripStopsList';
      var list = document.getElementById(listId);
      if (!list) return;
      var isLeg2 = listId === 'tripStopsList_2';
      var isLeg3 = listId === 'tripStopsList_3';
      data = data || { location: '', transportNeeded: '', tourGuideNeeded: '', pickup: '', dropoff: '', guideLocation: '', fareAmount: '' };
      var idx = isLeg3 ? stopIndex3++ : (isLeg2 ? stopIndex2++ : stopIndex++);
      var nameLoc = escTripAttr(data.location || '');
      var pu = escTripAttr(data.pickup || '');
      var dof = escTripAttr(data.dropoff || '');
      var gl = escTripAttr(data.guideLocation || '');
      var fareAmt = (data.fareAmount || '').toString().replace(/[^0-9.]/g, '');
      var trYes = (data.transportNeeded === 'yes') ? ' selected' : '';
      var trNo = (data.transportNeeded === 'no') ? ' selected' : '';
      var gYes = (data.tourGuideNeeded === 'yes') ? ' selected' : '';
      var gNo = (data.tourGuideNeeded === 'no') ? ' selected' : '';
      var card = document.createElement('div');
      card.className = 'trip-stop-card';
      card.dataset.stopIndex = idx;
      card.innerHTML =
        '<div class="trip-stop-card-header">' +
        '<h4 class="trip-stop-title">Stop ' + (list.children.length + 1) + '</h4>' +
        '<button type="button" class="btn-remove-stop" aria-label="Remove this stop"><i class="fa-solid fa-trash-can"></i> Remove</button>' +
        '</div>' +
        '<div class="form-group trip-stop-location-group"><label>Stop location / attraction</label><div class="trip-stop-location-input-wrap"><i class="fa-solid fa-location-dot trip-stop-location-icon"></i><input type="text" class="trip-stop-location" placeholder="Stop location / attraction" value="' + nameLoc + '"></div></div>' +
        '<div class="trip-stop-options">' +
        '<div class="trip-stop-option-group trip-stop-opt-transport">' +
        '<span class="trip-option-label">Transport Needed?</span>' +
        '<div class="trip-toggle-btns">' +
        '<button type="button" class="trip-toggle-btn' + trYes + '" data-value="yes">Yes</button>' +
        '<button type="button" class="trip-toggle-btn' + trNo + '" data-value="no">No</button>' +
        '</div></div>' +
        '<div class="trip-stop-option-group trip-stop-opt-guide">' +
        '<span class="trip-option-label">Tour Guide Needed?</span>' +
        '<div class="trip-toggle-btns">' +
        '<button type="button" class="trip-toggle-btn' + gYes + '" data-value="yes">Yes</button>' +
        '<button type="button" class="trip-toggle-btn' + gNo + '" data-value="no">No</button>' +
        '</div></div>' +
        '</div>' +
        '<div class="trip-stop-details-block">' +
        '<div class="trip-stop-transport-yes-fields" style="display:none">' +
        '<input type="hidden" class="trip-stop-pickup" value="' + pu + '">' +
        '<input type="hidden" class="trip-stop-dropoff" value="' + dof + '">' +
        '<input type="hidden" class="trip-stop-fare-amount" value="' + escTripAttr(fareAmt) + '">' +
        '<input type="hidden" class="trip-stop-tr-date" value="">' +
        '<input type="hidden" class="trip-stop-tr-vehicle" value="">' +
        '<input type="hidden" class="trip-stop-tr-time" value="">' +
        '<input type="hidden" class="trip-stop-tr-people" value="">' +
        '</div>' +
        '<div class="trip-stop-guide-yes-fields" style="display:none">' +
        '<input type="hidden" class="trip-stop-guide-location" value="' + gl + '">' +
        '<input type="hidden" class="trip-stop-guide-date" value="">' +
        '<input type="hidden" class="trip-stop-guide-language" value="">' +
        '<input type="hidden" class="trip-stop-guide-time" value="">' +
        '<input type="hidden" class="trip-stop-guide-notes" value="">' +
        '</div>' +
        '</div>' +
        '<div class="trip-stop-summary">' +
        '<p class="trip-stop-summary-label">Stop summary</p>' +
        '<div class="trip-stop-summary-body"></div>' +
        '</div>';
      list.appendChild(card);
      renumberStops(listId);
      var locInput = card.querySelector('.trip-stop-location');
      if (locInput) {
        var distHid = isLeg3 ? 'dest_district_hidden_3' : (isLeg2 ? 'dest_district_hidden_2' : 'dest_district_hidden');
        initStopLocationAutocomplete(locInput, distHid);
      }
      syncStopCardFields(card);
      updateStopCardSummary(card);
    }
    var placesAutocompleteDebounceTimer, placesAutocompleteAbort;
    function initStopLocationAutocomplete(inputEl, districtHiddenId) {
      if (!inputEl || inputEl.dataset.placesInit === 'true') return;
      inputEl.dataset.placesInit = 'true';
      districtHiddenId = districtHiddenId || 'dest_district_hidden';
      var dropdown = document.createElement('div');
      dropdown.className = 'trip-stop-location-suggestions';
      dropdown.setAttribute('role', 'listbox');
      dropdown.setAttribute('aria-hidden', 'true');
      var wrap = inputEl.closest('.trip-stop-location-input-wrap') || inputEl.parentNode;
      wrap.style.position = 'relative';
      wrap.appendChild(dropdown);
      function hideDropdown() {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
      }
      function showSuggestions(predictions) {
        dropdown.innerHTML = '';
        if (!predictions || predictions.length === 0) { hideDropdown(); return; }
        predictions.forEach(function (p) {
          var desc = (typeof p === 'object' && p !== null && p.description) ? p.description : (typeof p === 'string' ? p : '');
          if (!desc) return;
          var item = document.createElement('div');
          item.className = 'trip-stop-location-suggestion-item';
          item.setAttribute('role', 'option');
          item.textContent = desc;
          item.dataset.description = desc;
          if (p && p.place_id) item.dataset.placeId = p.place_id;
          item.addEventListener('mousedown', function (e) {
            e.preventDefault();
            inputEl.value = this.dataset.description || this.textContent;
            if (this.dataset.placeId) inputEl.dataset.placeId = this.dataset.placeId; else delete inputEl.dataset.placeId;
            hideDropdown();
            inputEl.dispatchEvent(new Event('input', { bubbles: true }));
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
      }
      function fetchSuggestions(query, done) {
        if (placesAutocompleteAbort) placesAutocompleteAbort.abort();
        if (!query || query.length < 2) { done([]); return; }
        var url = '<?php echo htmlspecialchars($places_autocomplete_url, ENT_QUOTES, 'UTF-8'); ?>?input=' + encodeURIComponent(query);
        var destHidden = document.getElementById(districtHiddenId);
        if (destHidden && destHidden.value) {
          url += '&district=' + encodeURIComponent(destHidden.value);
        }
        var xhr = new XMLHttpRequest();
        placesAutocompleteAbort = xhr;
        xhr.onreadystatechange = function () {
          if (xhr.readyState !== 4) return;
          if (placesAutocompleteAbort !== xhr) return;
          if (xhr.status === 200) {
            try {
              var data = JSON.parse(xhr.responseText);
              var list = Array.isArray(data.predictions) ? data.predictions : [];
              done(list);
            } catch (e) { done([]); }
          } else { done([]); }
        };
        xhr.open('GET', url, true);
        xhr.send();
      }
      inputEl.addEventListener('input', function () {
        delete inputEl.dataset.placeId;
        clearTimeout(placesAutocompleteDebounceTimer);
        var q = inputEl.value.trim();
        if (q.length < 2) { hideDropdown(); return; }
        placesAutocompleteDebounceTimer = setTimeout(function () { fetchSuggestions(q, showSuggestions); }, 280);
      });
      inputEl.addEventListener('focus', function () {
        var q = inputEl.value.trim();
        if (q.length >= 2) fetchSuggestions(q, showSuggestions);
      });
      inputEl.addEventListener('blur', function () { setTimeout(hideDropdown, 220); });
    }
    function renumberStops(listId) {
      listId = listId || 'tripStopsList';
      var list = document.getElementById(listId);
      if (!list) return;
      var cards = list.querySelectorAll('.trip-stop-card');
      cards.forEach(function (card, i) {
        var title = card.querySelector('.trip-stop-title');
        if (title) title.textContent = 'Stop ' + (i + 1);
        updateStopCardSummary(card);
      });
      if (listId === 'tripStopsList_2') {
        clearTripStopsErrorIfAllLocationsOk2();
        updateAddStopsButtonLabel2();
      } else if (listId === 'tripStopsList_3') {
        clearTripStopsErrorIfAllLocationsOk3();
        updateAddStopsButtonLabel3();
      } else {
        clearTripStopsErrorIfAllLocationsOk();
        updateAddStopsButtonLabel();
      }
    }
    function clearTripStopsStepError2() {
      var el = document.getElementById('tripStopsError_2');
      if (el) {
        el.textContent = '';
        el.classList.remove('trip-date-error--visible');
      }
    }
    function clearTripStopsErrorIfAllLocationsOk2() {
      var list = document.getElementById('tripStopsList_2');
      if (!list) return;
      var cards = list.querySelectorAll('.trip-stop-card');
      if (cards.length < 1) return;
      for (var i = 0; i < cards.length; i++) {
        var locInput = cards[i].querySelector('.trip-stop-location');
        if (!(((locInput && locInput.value) || '').trim())) return;
      }
      clearTripStopsStepError2();
    }
    function clearTripStopsStepError3() {
      var el = document.getElementById('tripStopsError_3');
      if (el) {
        el.textContent = '';
        el.classList.remove('trip-date-error--visible');
      }
    }
    function clearTripStopsErrorIfAllLocationsOk3() {
      var list = document.getElementById('tripStopsList_3');
      if (!list) return;
      var cards = list.querySelectorAll('.trip-stop-card');
      if (cards.length < 1) return;
      for (var i = 0; i < cards.length; i++) {
        var locInput = cards[i].querySelector('.trip-stop-location');
        if (!(((locInput && locInput.value) || '').trim())) return;
      }
      clearTripStopsStepError3();
    }
    function updateAddStopsButtonLabel3() {
      var btn = document.getElementById('btnAddMoreStops_3');
      var list = document.getElementById('tripStopsList_3');
      if (!btn || !list) return;
      var n = list.querySelectorAll('.trip-stop-card').length;
      btn.innerHTML = '<i class="fa-solid fa-plus"></i> ' + (n === 0 ? 'Add a stop' : 'Add more stops');
    }
    function updateStopsHeading() {
      var heading = document.getElementById('tripStopsHeading');
      var destInput = document.getElementById('dest_primary');
      if (!heading || !destInput) return;
      var name = (destInput.value || '').trim();
      heading.textContent = name ? 'Stops in ' + name : 'Stops in this area';
    }
    var destCities = <?php echo json_encode(array_keys($main_cities)); ?>;
    var destCityToDistrict = <?php echo json_encode($main_cities); ?>;
    (function initDestAutocomplete() {
      var input = document.getElementById('dest_primary');
      var hidden = document.getElementById('dest_district_hidden');
      var dropdown = document.getElementById('dest_suggestions');
      if (!input || !hidden || !dropdown) return;
      function hideDestDropdown() {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
        input.setAttribute('aria-expanded', 'false');
      }
      function showDestSuggestions(list) {
        dropdown.innerHTML = '';
        if (!list || list.length === 0) { hideDestDropdown(); return; }
        list.forEach(function (city) {
          var district = destCityToDistrict[city];
          var item = document.createElement('div');
          item.className = 'trip-dest-suggestion-item';
          item.setAttribute('role', 'option');
          item.textContent = city;
          item.addEventListener('mousedown', function (e) {
            e.preventDefault();
            input.value = city;
            hidden.value = district || '';
            hideDestDropdown();
            updateStopsHeading();
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
        input.setAttribute('aria-expanded', 'true');
      }
      function filterCities(q) {
        if (!q) return destCities.slice();
        q = q.toLowerCase();
        return destCities.filter(function (c) { return c.toLowerCase().indexOf(q) !== -1; });
      }
      input.addEventListener('input', function () {
        var q = input.value.trim();
        showDestSuggestions(filterCities(q));
      });
      input.addEventListener('focus', function () {
        showDestSuggestions(filterCities(input.value.trim()));
      });
      input.addEventListener('blur', function () {
        setTimeout(function () {
          hideDestDropdown();
          var v = input.value.trim();
          if (v && destCityToDistrict[v]) hidden.value = destCityToDistrict[v];
        }, 220);
      });
      updateStopsHeading();
    })();
    function updateStopsHeading2() {
      var heading = document.getElementById('tripStopsHeading_2');
      var destInput = document.getElementById('dest_primary_2');
      if (!heading || !destInput) return;
      var name = (destInput.value || '').trim();
      heading.textContent = name ? 'Stops in ' + name : 'Stops in this area';
    }
    function initDestAutocomplete2() {
      if (destAutocomplete2Inited) return;
      var input = document.getElementById('dest_primary_2');
      var hidden = document.getElementById('dest_district_hidden_2');
      var dropdown = document.getElementById('dest_suggestions_2');
      if (!input || !hidden || !dropdown) return;
      destAutocomplete2Inited = true;
      function hideDestDropdown() {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
        input.setAttribute('aria-expanded', 'false');
      }
      function showDestSuggestions(list) {
        dropdown.innerHTML = '';
        if (!list || list.length === 0) { hideDestDropdown(); return; }
        list.forEach(function (city) {
          var district = destCityToDistrict[city];
          var item = document.createElement('div');
          item.className = 'trip-dest-suggestion-item';
          item.setAttribute('role', 'option');
          item.textContent = city;
          item.addEventListener('mousedown', function (e) {
            e.preventDefault();
            input.value = city;
            hidden.value = district || '';
            hideDestDropdown();
            updateStopsHeading2();
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
        input.setAttribute('aria-expanded', 'true');
      }
      function filterCities(q) {
        if (!q) return destCities.slice();
        q = q.toLowerCase();
        return destCities.filter(function (c) { return c.toLowerCase().indexOf(q) !== -1; });
      }
      input.addEventListener('input', function () {
        var q = input.value.trim();
        showDestSuggestions(filterCities(q));
      });
      input.addEventListener('focus', function () {
        showDestSuggestions(filterCities(input.value.trim()));
      });
      input.addEventListener('blur', function () {
        setTimeout(function () {
          hideDestDropdown();
          var v = input.value.trim();
          if (v && destCityToDistrict[v]) hidden.value = destCityToDistrict[v];
        }, 220);
      });
      updateStopsHeading2();
    }
    function updateStopsHeading3() {
      var heading = document.getElementById('tripStopsHeading_3');
      var destInput = document.getElementById('dest_primary_3');
      if (!heading || !destInput) return;
      var name = (destInput.value || '').trim();
      heading.textContent = name ? 'Stops in ' + name : 'Stops in this area';
    }
    function initDestAutocomplete3() {
      if (destAutocomplete3Inited) return;
      var input = document.getElementById('dest_primary_3');
      var hidden = document.getElementById('dest_district_hidden_3');
      var dropdown = document.getElementById('dest_suggestions_3');
      if (!input || !hidden || !dropdown) return;
      destAutocomplete3Inited = true;
      function hideDestDropdown() {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
        input.setAttribute('aria-expanded', 'false');
      }
      function showDestSuggestions(list) {
        dropdown.innerHTML = '';
        if (!list || list.length === 0) { hideDestDropdown(); return; }
        list.forEach(function (city) {
          var district = destCityToDistrict[city];
          var item = document.createElement('div');
          item.className = 'trip-dest-suggestion-item';
          item.setAttribute('role', 'option');
          item.textContent = city;
          item.addEventListener('mousedown', function (e) {
            e.preventDefault();
            input.value = city;
            hidden.value = district || '';
            hideDestDropdown();
            updateStopsHeading3();
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
        input.setAttribute('aria-expanded', 'true');
      }
      function filterCities(q) {
        if (!q) return destCities.slice();
        q = q.toLowerCase();
        return destCities.filter(function (c) { return c.toLowerCase().indexOf(q) !== -1; });
      }
      input.addEventListener('input', function () {
        var q = input.value.trim();
        showDestSuggestions(filterCities(q));
      });
      input.addEventListener('focus', function () {
        showDestSuggestions(filterCities(input.value.trim()));
      });
      input.addEventListener('blur', function () {
        setTimeout(function () {
          hideDestDropdown();
          var v = input.value.trim();
          if (v && destCityToDistrict[v]) hidden.value = destCityToDistrict[v];
        }, 220);
      });
      updateStopsHeading3();
    }
    document.getElementById('btnAddMoreStops').addEventListener('click', function () {
      addStopCard();
    });
    var btnAdd2 = document.getElementById('btnAddMoreStops_2');
    if (btnAdd2) btnAdd2.addEventListener('click', function () { addStopCard(null, { listId: 'tripStopsList_2' }); });
    var btnAdd3 = document.getElementById('btnAddMoreStops_3');
    if (btnAdd3) btnAdd3.addEventListener('click', function () { addStopCard(null, { listId: 'tripStopsList_3' }); });
    function attachTripStopsListStepHandlers(listId) {
      var listEl = document.getElementById(listId);
      if (!listEl) return;
      var isLeg2 = listId === 'tripStopsList_2';
      var isLeg3 = listId === 'tripStopsList_3';
      listEl.addEventListener('click', function (e) {
        var removeBtn = e.target.closest('.btn-remove-stop');
        if (removeBtn) {
          var card = removeBtn.closest('.trip-stop-card');
          if (card) { card.remove(); renumberStops(listId); }
          return;
        }
        var toggleBtn = e.target.closest('.trip-toggle-btn');
        if (toggleBtn) {
          e.preventDefault();
          e.stopPropagation();
          var group = toggleBtn.closest('.trip-stop-option-group');
          if (group) {
            group.querySelectorAll('.trip-toggle-btn').forEach(function (b) { b.classList.remove('selected'); });
            toggleBtn.classList.add('selected');
            var card = group.closest('.trip-stop-card');
            var isTransportGroup = group.classList.contains('trip-stop-opt-transport');
            var isGuideGroup = group.classList.contains('trip-stop-opt-guide');
            var isYes = toggleBtn.getAttribute('data-value') === 'yes';
            if (card) {
              if (isTransportGroup && !isYes) {
                clearTransportStopData(card);
              }
              if (isGuideGroup && !isYes) {
                clearGuideStopData(card);
              }
              syncStopCardFields(card);
              if (isYes && isTransportGroup) {
                openTransportModalForStop(card);
              } else if (isYes && isGuideGroup) {
                openTourGuideModalForStop(card);
              }
              updateStopCardSummary(card);
              if (isLeg3) clearTripStopsStepError3();
              else if (isLeg2) clearTripStopsStepError2();
              else clearTripStopsStepError();
            }
          }
        }
      });
      listEl.addEventListener('input', function (e) {
        var t = e.target;
        if (!t || !t.classList || !t.classList.contains('trip-stop-location')) return;
        var card = t.closest('.trip-stop-card');
        if (card) updateStopCardSummary(card);
        if (isLeg3) clearTripStopsErrorIfAllLocationsOk3();
        else if (isLeg2) clearTripStopsErrorIfAllLocationsOk2();
        else clearTripStopsErrorIfAllLocationsOk();
      });
    }
    attachTripStopsListStepHandlers('tripStopsList');
    attachTripStopsListStepHandlers('tripStopsList_2');
    attachTripStopsListStepHandlers('tripStopsList_3');

    (function bindTripWizardDraftListeners() {
      var root = document.querySelector('main.trip-main-content');
      if (!root) return;
      ['input', 'change'].forEach(function (ev) {
        root.addEventListener(ev, scheduleSaveTripWizardDraft, true);
      });
      root.addEventListener('click', function () {
        scheduleSaveTripWizardDraft();
      }, true);
    })();
    window.addEventListener('pagehide', function () {
      try { saveTripWizardDraftNow(); } catch (ePh) {}
    });

    var transportModalOverlay = document.getElementById('transportRequestModalOverlay');
    function closeTransportModal() {
      tripStopCardForTransport = null;
      if (transportModalOverlay) {
        transportModalOverlay.classList.remove('trip-modal-open');
        transportModalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
      if (typeof showTransportForm === 'function') showTransportForm();
    }
    if (document.getElementById('transportRequestModalClose')) {
      document.getElementById('transportRequestModalClose').addEventListener('click', closeTransportModal);
    }
    if (document.getElementById('transportRequestModalCancel')) {
      document.getElementById('transportRequestModalCancel').addEventListener('click', closeTransportModal);
    }
    if (transportModalOverlay) {
      transportModalOverlay.addEventListener('click', function (e) {
        if (e.target === transportModalOverlay) closeTransportModal();
      });
    }
    var vehicleCapacities = { 'Tuk': 3, 'Car': 4, 'Minivan': 7, 'Minivan AC': 7, 'Bus': 20, 'Bus AC': 20 };
    var transportForm = document.getElementById('transportRequestForm');
    var trFormWrap = document.getElementById('tr_formWrap');
    var trSuccessState = document.getElementById('tr_successState');
    var transportSubmitUrl = '/CeylonGo/public/tourist/transport-services';
    var lastSubmittedNumPeople = 0;
    function showTransportForm() {
      if (trFormWrap) trFormWrap.style.display = '';
      if (trSuccessState) trSuccessState.style.display = 'none';
    }
    function showTransportSuccess() {
      if (trFormWrap) trFormWrap.style.display = 'none';
      if (trSuccessState) trSuccessState.style.display = 'block';
    }
    function resetTransportFormForAnother() {
      var vehicleSelect = document.getElementById('tr_vehicleType');
      var numPeopleEl = document.getElementById('tr_numPeople');
      var estimatedFare = document.getElementById('tr_estimatedFare');
      var fareValEl = document.getElementById('tr_estimatedFareValue');
      var distValEl = document.getElementById('tr_distanceValue');
      var breakdown = document.getElementById('tr_fareBreakdown');
      var errEl = document.getElementById('tr_vehicleError');
      if (vehicleSelect) vehicleSelect.value = '';
      if (numPeopleEl) {
        var adultsEl = document.getElementById('adults');
        var childrenEl = document.getElementById('children');
        var infantsEl = document.getElementById('infants');
        var total = (parseInt(adultsEl && adultsEl.value, 10) || 0) + (parseInt(childrenEl && childrenEl.value, 10) || 0) + (parseInt(infantsEl && infantsEl.value, 10) || 0);
        var remaining = total - lastSubmittedNumPeople;
        numPeopleEl.value = Math.max(1, remaining);
      }
      if (estimatedFare) estimatedFare.value = 'LKR 0.00';
      if (fareValEl) fareValEl.value = '';
      if (distValEl) distValEl.value = '';
      if (breakdown) breakdown.style.display = 'none';
      if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }
      var confirmBtn = document.getElementById('tr_btnConfirm');
      if (confirmBtn) confirmBtn.disabled = true;
    }
    if (document.getElementById('tr_btnAddAnother')) {
      document.getElementById('tr_btnAddAnother').addEventListener('click', function () {
        resetTransportFormForAnother();
        showTransportForm();
      });
    }
    if (document.getElementById('tr_btnDone')) {
      document.getElementById('tr_btnDone').addEventListener('click', function () {
        window.location.href = '/CeylonGo/public/tourist/transport-report';
      });
    }
    if (transportForm) {
      transportForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var vehicleSelect = document.getElementById('tr_vehicleType');
        var numPeopleEl = document.getElementById('tr_numPeople');
        var errorEl = document.getElementById('tr_vehicleError');
        if (!vehicleSelect || !numPeopleEl || !errorEl) return;
        var vehicle = vehicleSelect.value;
        var numPeople = parseInt(numPeopleEl.value, 10) || 0;
        var capacity = vehicleCapacities[vehicle];
        errorEl.style.display = 'none';
        errorEl.textContent = '';
        if (vehicle && capacity !== undefined && numPeople > capacity) {
          errorEl.textContent = 'This vehicle accommodates up to ' + capacity + ' people. Please choose a larger vehicle or reduce the number of people.';
          errorEl.style.display = 'block';
          return;
        }
        var submitBtn = transportForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        var formData = new FormData(transportForm);
        fetch(transportSubmitUrl, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (submitBtn) submitBtn.disabled = false;
            if (data && data.success) {
              lastSubmittedNumPeople = parseInt(numPeopleEl ? numPeopleEl.value : 0, 10) || 0;
              if (tripStopCardForTransport) {
                var trSt = (data && data.status) ? String(data.status) : 'pending';
                tripStopCardForTransport.setAttribute('data-transport-booking-status', trSt);
                if (data && data.request_id != null) {
                  tripStopCardForTransport.setAttribute('data-transport-request-id', String(data.request_id));
                }
                applyTransportModalToStopCard();
              } else {
                showTransportSuccess();
              }
            } else {
              alert(data && data.error ? data.error : 'Failed to submit transport request.');
            }
          })
          .catch(function () {
            if (submitBtn) submitBtn.disabled = false;
            alert('An error occurred. Please try again.');
          });
      });
      document.getElementById('tr_vehicleType').addEventListener('change', function () {
        var err = document.getElementById('tr_vehicleError');
        if (err) { err.style.display = 'none'; err.textContent = ''; }
      });
    }
    var trPickupInput = document.getElementById('tr_pickupLocation');
    var trDropoffInput = document.getElementById('tr_dropoffLocation');
    if (trPickupInput) initStopLocationAutocomplete(trPickupInput);
    if (trDropoffInput) initStopLocationAutocomplete(trDropoffInput);
    var tgLocationInput = document.getElementById('tg_location');
    if (tgLocationInput) initStopLocationAutocomplete(tgLocationInput);

    var trBtnCalculate = document.getElementById('tr_btnCalculate');
    var trEstimatedFare = document.getElementById('tr_estimatedFare');
    var placesAutocompleteUrl = '<?php echo htmlspecialchars($places_autocomplete_url ? $places_autocomplete_url : '/CeylonGo/public/api/places-autocomplete', ENT_QUOTES, 'UTF-8'); ?>';
    var calculateFareUrl = (placesAutocompleteUrl.split('/api/')[0] || '') + '/api/calculate-fare';
    if (trBtnCalculate && trEstimatedFare) {
      trBtnCalculate.addEventListener('click', function () {
        var pickup = (document.getElementById('tr_pickupLocation') || {}).value.trim();
        var dropoff = (document.getElementById('tr_dropoffLocation') || {}).value.trim();
        var vehicleType = (document.getElementById('tr_vehicleType') || {}).value;
        if (!pickup || !dropoff) {
          alert('Please enter both pickup and dropoff locations');
          return;
        }
        if (!vehicleType) {
          alert('Please select a vehicle type');
          return;
        }
        trEstimatedFare.value = 'Calculating...';
        trBtnCalculate.disabled = true;
        var params = 'pickup=' + encodeURIComponent(pickup) + '&dropoff=' + encodeURIComponent(dropoff) + '&vehicleType=' + encodeURIComponent(vehicleType);
        var pickupPlaceId = (trPickupInput && trPickupInput.dataset && trPickupInput.dataset.placeId) ? trPickupInput.dataset.placeId : '';
        var dropoffPlaceId = (trDropoffInput && trDropoffInput.dataset && trDropoffInput.dataset.placeId) ? trDropoffInput.dataset.placeId : '';
        if (pickupPlaceId) params += '&pickup_place_id=' + encodeURIComponent(pickupPlaceId);
        if (dropoffPlaceId) params += '&dropoff_place_id=' + encodeURIComponent(dropoffPlaceId);
        fetch(calculateFareUrl + '?' + params)
          .then(function (r) { return r.json(); })
          .then(function (data) {
            trBtnCalculate.disabled = false;
            if (data && data.success) {
              trEstimatedFare.value = 'LKR ' + Number(data.totalFare).toFixed(2);
              var fareValEl = document.getElementById('tr_estimatedFareValue');
              var distValEl = document.getElementById('tr_distanceValue');
              if (fareValEl) fareValEl.value = String(data.totalFare || '');
              if (distValEl) distValEl.value = String(data.distance || '');
              var confirmBtn = document.getElementById('tr_btnConfirm');
              if (confirmBtn) confirmBtn.disabled = false;
              var breakdown = document.getElementById('tr_fareBreakdown');
              var distEl = document.getElementById('tr_fareDistance');
              var rateEl = document.getElementById('tr_fareBaseRate');
              var totalEl = document.getElementById('tr_fareTotal');
              if (breakdown && distEl && rateEl && totalEl) {
                distEl.textContent = (data.distance || 0) + ' km';
                rateEl.textContent = 'LKR ' + (data.baseRate || 0) + '/km';
                totalEl.textContent = 'LKR ' + Number(data.totalFare).toFixed(2);
                breakdown.style.display = 'block';
              }
            } else {
              trEstimatedFare.value = 'LKR 0.00';
              var fareValEl = document.getElementById('tr_estimatedFareValue');
              var distValEl = document.getElementById('tr_distanceValue');
              if (fareValEl) fareValEl.value = '';
              if (distValEl) distValEl.value = '';
              var breakdown = document.getElementById('tr_fareBreakdown');
              if (breakdown) breakdown.style.display = 'none';
              alert(data.error || 'Could not calculate fare. Please check the locations.');
            }
          })
          .catch(function () {
            trBtnCalculate.disabled = false;
            trEstimatedFare.value = 'LKR 0.00';
            var fareValEl = document.getElementById('tr_estimatedFareValue');
            var distValEl = document.getElementById('tr_distanceValue');
            if (fareValEl) fareValEl.value = '';
            if (distValEl) distValEl.value = '';
            var breakdown = document.getElementById('tr_fareBreakdown');
            if (breakdown) breakdown.style.display = 'none';
            alert('An error occurred. Please try again.');
          });
      });
    }

    var tourGuideModalOverlay = document.getElementById('tourGuideRequestModalOverlay');
    function closeTourGuideModal() {
      tripStopCardForGuide = null;
      if (tourGuideModalOverlay) {
        tourGuideModalOverlay.classList.remove('trip-modal-open');
        tourGuideModalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
    }
    if (document.getElementById('tourGuideRequestModalClose')) {
      document.getElementById('tourGuideRequestModalClose').addEventListener('click', closeTourGuideModal);
    }
    if (document.getElementById('tourGuideRequestModalCancel')) {
      document.getElementById('tourGuideRequestModalCancel').addEventListener('click', closeTourGuideModal);
    }
    if (tourGuideModalOverlay) {
      tourGuideModalOverlay.addEventListener('click', function (e) {
        if (e.target === tourGuideModalOverlay) closeTourGuideModal();
      });
    }

    var tourGuideRequestForm = document.getElementById('tourGuideRequestForm');
    if (tourGuideRequestForm) {
      tourGuideRequestForm.addEventListener('submit', function (e) {
        // Do NOT save to DB yet. Queue the guide request and only persist after "Submit trip".
        e.preventDefault();
        if (!tripStopCardForGuide) {
          alert('Please open the request from a stop in your trip.');
          return;
        }
        var cardG = tripStopCardForGuide;
        var tgLocEl = document.getElementById('tg_location');
        var tgDateEl = document.getElementById('tg_date');
        var tgLangEl = document.getElementById('tg_language');
        var tgTimeEl = document.getElementById('tg_time');
        var tgNotesEl = document.getElementById('tg_notes');
        var loc = ((tgLocEl || {}).value || '').trim();
        var dt = ((tgDateEl || {}).value || '').trim();
        var lang = ((tgLangEl || {}).value || '').trim();
        var tm = ((tgTimeEl || {}).value || '').trim();
        if (!loc || !dt || !lang || !tm) {
          alert('Please fill location, date, language and time.');
          return;
        }

        // Store on the stop card (hidden inputs already exist).
        var gl = cardG.querySelector('.trip-stop-guide-location');
        if (gl) gl.value = loc;
        function setGh(sel, value) {
          var h = cardG.querySelector(sel);
          if (h) h.value = (value || '').trim();
        }
        setGh('.trip-stop-guide-date', dt);
        setGh('.trip-stop-guide-language', lang);
        setGh('.trip-stop-guide-time', tm);
        setGh('.trip-stop-guide-notes', tgNotesEl ? (tgNotesEl.value || '') : '');

        // Mark as queued/pending locally so validations pass.
        cardG.setAttribute('data-guide-booking-status', 'pending');
        cardG.setAttribute('data-guide-queued', '1');

        updateStopCardSummary(cardG);
        if (isStopCardInLeg3(cardG)) clearTripStopsStepError3();
        else if (isStopCardInLeg2(cardG)) clearTripStopsStepError2();
        else clearTripStopsStepError();

        tripStopCardForGuide = null;
        closeTourGuideModal();
      });
    }

    function registerTripAccommodation(prefix, dateStartId, dateEndId) {
      function gid(s) {
        return document.getElementById(prefix + s);
      }
      function roomOpts() {
        return window[prefix + 'AccommodationRoomOptions'] || {};
      }
      var detailsModalHotelName = '';
      var detailsModalHotelId = '';

      function search(e) {
        e.preventDefault();
        var raw = (gid('AccommodationSearchInput') || {}).value || '';
        var term = raw.toLowerCase().trim();
        var grid = gid('AccommodationHotelsGrid');
        if (!grid) return;
        grid.querySelectorAll('.hotel-card').forEach(function (card) {
          var name = (card.querySelector('.hotel-name') || {}).textContent || '';
          var loc = (card.querySelector('.hotel-location') || {}).textContent || '';
          var nameLower = name.toLowerCase();
          var locLower = loc.toLowerCase();
          var match = !term || nameLower.indexOf(term) !== -1 || locLower.indexOf(term) !== -1;
          card.style.display = match ? '' : 'none';
        });
      }
      function applyFilters() {
        var price = (gid('AccommodationPriceFilter') || {}).value;
        var rating = (gid('AccommodationRatingFilter') || {}).value;
        var location = (gid('AccommodationLocationFilter') || {}).value;
        var grid = gid('AccommodationHotelsGrid');
        if (!grid) return;
        grid.querySelectorAll('.hotel-card').forEach(function (card) {
          var show = true;
          if (price && card.dataset.price !== price) show = false;
          if (rating && card.dataset.rating !== rating) show = false;
          if (location && card.dataset.location.indexOf(location) === -1) show = false;
          card.style.display = show ? '' : 'none';
        });
      }
      function closeModal() {
        var modal = gid('AccommodationBookingModal');
        if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
      }
      function openDetailsModal(card) {
        if (!card) return;
        var hotelIdFromCard = card.dataset.hotelId || '';
        detailsModalHotelId = hotelIdFromCard;
        var hidIdEarly = gid('AccommodationHotelId');
        var hidNameEarly = gid('AccommodationHotelName');
        var nameElEarly = card.querySelector('.hotel-name');
        if (hidIdEarly) hidIdEarly.value = hotelIdFromCard;
        if (hidNameEarly && nameElEarly) hidNameEarly.value = nameElEarly.textContent.trim();
        var imgEl = card.querySelector('.hotel-image');
        var nameEl = card.querySelector('.hotel-name');
        var locEl = card.querySelector('.hotel-location');
        var ratingEl = card.querySelector('.hotel-rating');
        var amenitiesEl = card.querySelector('.hotel-amenities');
        var priceEl = card.querySelector('.hotel-price');
        var imgStyle = imgEl ? imgEl.getAttribute('style') || '' : '';
        var imgUrl = imgStyle.match(/url\(['"]?([^'"]+)['"]?\)/);
        gid('DetailsModalImage').style.backgroundImage = imgUrl ? 'url(' + imgUrl[1] + ')' : 'none';
        gid('DetailsModalName').textContent = nameEl ? nameEl.textContent.trim() : '';
        gid('DetailsModalLocation').textContent = locEl ? locEl.textContent.trim() : '';
        gid('DetailsModalRating').innerHTML = ratingEl ? ratingEl.innerHTML : '';
        gid('DetailsModalAmenities').innerHTML = amenitiesEl ? amenitiesEl.innerHTML : '';
        gid('DetailsModalPrice').innerHTML = priceEl ? priceEl.innerHTML : '';
        detailsModalHotelName = nameEl ? nameEl.textContent.trim() : '';
        var roomsRoot = gid('DetailsModalRooms');
        if (roomsRoot) {
          roomsRoot.innerHTML = '';
          var hotelId = hotelIdFromCard;
          var roomDataMap = roomOpts();
          var rooms = roomDataMap[hotelId] || [];
          if (rooms.length) {
            rooms.forEach(function (room) {
              var wrapper = document.createElement('div');
              wrapper.className = 'modal-room-card';
              var img = document.createElement('div');
              img.className = 'modal-room-image';
              if (room.image) img.style.backgroundImage = 'url(' + room.image + ')';
              var info = document.createElement('div');
              info.className = 'modal-room-info';
              var typeEl = document.createElement('div');
              typeEl.className = 'modal-room-type';
              typeEl.textContent = room.type || '';
              var descEl = document.createElement('div');
              descEl.className = 'modal-room-desc';
              descEl.textContent = room.description || '';
              var priceEl2 = document.createElement('div');
              priceEl2.className = 'modal-room-price';
              priceEl2.textContent = room.price || '';
              info.appendChild(typeEl);
              info.appendChild(descEl);
              info.appendChild(priceEl2);
              wrapper.appendChild(img);
              wrapper.appendChild(info);
              roomsRoot.appendChild(wrapper);
            });
          }
        }
        var modal = gid('AccommodationDetailsModal');
        if (modal) { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
      }
      function closeDetailsModal() {
        var modal = gid('AccommodationDetailsModal');
        if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
      }
      function populateBookingForm(hotelId, hotelName) {
        var hidName = gid('AccommodationHotelName');
        var hidId = gid('AccommodationHotelId');
        if (hidName) hidName.value = hotelName || '';
        if (hidId) hidId.value = hotelId || '';

        var nameInput = gid('AccommodationCustomerName');
        var contactInput = gid('AccommodationContact');
        var guestsInput = gid('AccommodationGuests');
        if (nameInput) nameInput.value = tripUserName || '';
        if (contactInput) contactInput.value = tripUserContact || '';
        if (guestsInput) {
          var a = parseInt(adultsInput ? adultsInput.value : '0', 10) || 0;
          var c = parseInt(childrenInput ? childrenInput.value : '0', 10) || 0;
          var i = parseInt(infantsInput ? infantsInput.value : '0', 10) || 0;
          var total = a + c + i;
          guestsInput.value = total || 1;
        }

        var startEl = document.getElementById(dateStartId);
        var endEl = document.getElementById(dateEndId);
        var checkInEl = gid('AccommodationCheckIn');
        var checkOutEl = gid('AccommodationCheckOut');
        var nightsHidden = gid('AccommodationNights');
        var nights = 1;
        if (startEl && endEl && startEl.value && endEl.value) {
          var s = new Date(startEl.value);
          var e = new Date(endEl.value);
          var diff = Math.round((e - s) / (24 * 60 * 60 * 1000));
          if (diff > 0) nights = diff;
          if (checkInEl) { checkInEl.value = startEl.value; checkInEl.min = startEl.value; }
          if (checkOutEl) { checkOutEl.value = endEl.value; checkOutEl.min = startEl.value; }
        }
        if (nightsHidden) nightsHidden.value = nights;

        var roomSelect = gid('AccommodationRoomType');
        var roomCountInput = gid('AccommodationRoomCount');
        var totalPriceInput = gid('AccommodationTotalPrice');

        if (roomSelect) {
          while (roomSelect.firstChild) roomSelect.removeChild(roomSelect.firstChild);
          var opt = document.createElement('option');
          opt.value = '';
          opt.textContent = 'Select Room Type';
          roomSelect.appendChild(opt);
          var roomMap = roomOpts();
          var rooms = roomMap[hotelId] || [];
          rooms.forEach(function (room) {
            var o = document.createElement('option');
            o.value = room.type || '';
            o.textContent = (room.type || '') + (room.price ? ' – ' + room.price + ' / night' : '');
            if (room.priceValue != null && room.priceValue !== '') {
              o.setAttribute('data-price-per-night', String(room.priceValue));
              o.dataset.pricePerNight = String(room.priceValue);
            }
            roomSelect.appendChild(o);
          });
          if (rooms.length > 0 && roomSelect.options.length > 1) {
            roomSelect.selectedIndex = 1;
          }
        }
        if (roomCountInput) roomCountInput.value = 1;
        if (totalPriceInput) totalPriceInput.value = 'Rs.0.00';

        updateTotalPrice();
      }
      function updateTotalPrice() {
        var roomSelect = gid('AccommodationRoomType');
        var roomCountInput = gid('AccommodationRoomCount');
        var nightsHidden = gid('AccommodationNights');
        var totalPriceInput = gid('AccommodationTotalPrice');
        if (!roomSelect || !roomCountInput || !nightsHidden || !totalPriceInput) return;
        var nights = parseInt(nightsHidden.value || '1', 10) || 1;
        var count = parseInt(roomCountInput.value || '1', 10) || 1;
        var pricePerNight = 0;
        var selected = roomSelect.options[roomSelect.selectedIndex];
        if (selected) {
          var ppn = selected.dataset.pricePerNight;
          if (ppn == null || ppn === '') ppn = selected.getAttribute('data-price-per-night');
          if (ppn) pricePerNight = parseFloat(ppn) || 0;
        }
        var total = pricePerNight * nights * count;
        if (total <= 0) {
          totalPriceInput.value = 'Rs.0.00';
        } else {
          totalPriceInput.value = 'Rs.' + total.toLocaleString('en-LK', { minimumFractionDigits: 0 });
        }
        var totalNumericInput = gid('AccommodationTotalPriceNumeric');
        if (totalNumericInput) totalNumericInput.value = total > 0 ? total : 0;
      }
      function openBookingFromDetails() {
        closeDetailsModal();
        var hid = gid('AccommodationHotelName');
        var hotelIdInput = gid('AccommodationHotelId');
        var hotelName = detailsModalHotelName || (hid ? hid.value : '');
        var hotelId = (hotelIdInput && hotelIdInput.value) ? hotelIdInput.value.trim() : '';
        if (!hotelId) hotelId = detailsModalHotelId || '';
        populateBookingForm(hotelId, hotelName);
        var bookingModal = gid('AccommodationBookingModal');
        if (bookingModal) { bookingModal.classList.add('active'); }
      }
      function confirmBooking() {
        var checkIn = gid('AccommodationCheckIn');
        var checkOut = gid('AccommodationCheckOut');
        var roomType = gid('AccommodationRoomType');
        var totalPriceNumeric = gid('AccommodationTotalPriceNumeric');
        if (!checkIn || !checkIn.value || !checkOut || !checkOut.value || !roomType || !roomType.value) {
          alert('Please fill in all required fields.');
          return false;
        }
        if (totalPriceNumeric && (!totalPriceNumeric.value || parseFloat(totalPriceNumeric.value) <= 0)) {
          alert('Please select a room type so we can calculate the total price.');
          return false;
        }
        return true;
      }
      window[prefix + 'AccommodationSearch'] = search;
      window[prefix + 'AccommodationCloseDetailsModal'] = closeDetailsModal;
      window[prefix + 'AccommodationCloseModal'] = closeModal;
      window[prefix + 'AccommodationOpenBookingFromDetails'] = openBookingFromDetails;
      window[prefix + 'AccommodationConfirmBooking'] = confirmBooking;

      var accRootSel;
      if (prefix === 'trip2') accRootSel = '.trip-accommodation-content.trip-accommodation-content--secondary';
      else if (prefix === 'trip3') accRootSel = '.trip-accommodation-content.trip-accommodation-content--tertiary';
      else accRootSel = '.trip-accommodation-content:not(.trip-accommodation-content--secondary):not(.trip-accommodation-content--tertiary)';
      var accRoot = document.querySelector(accRootSel);
      if (accRoot) {
        [prefix + 'AccommodationPriceFilter', prefix + 'AccommodationRatingFilter', prefix + 'AccommodationLocationFilter'].forEach(function (id) {
          var el = document.getElementById(id);
          if (el) el.addEventListener('change', applyFilters);
        });
        var roomSelectEl = gid('AccommodationRoomType');
        var roomCountEl = gid('AccommodationRoomCount');
        if (roomSelectEl) roomSelectEl.addEventListener('change', updateTotalPrice);
        if (roomCountEl) roomCountEl.addEventListener('input', updateTotalPrice);
        accRoot.addEventListener('click', function (e) {
          var detailsBtn = e.target.closest('.btn-details[data-view-details]');
          if (detailsBtn) {
            e.preventDefault();
            var card = detailsBtn.closest('.hotel-card');
            if (card) openDetailsModal(card);
            return;
          }
          var btn = e.target.closest('.btn-book');
          if (btn && btn.href) {
            e.preventDefault();
            var card = btn.closest('.hotel-card');
            var nameEl = card ? card.querySelector('.hotel-name') : null;
            var hotelName = nameEl ? nameEl.textContent.trim() : '';
            var hotelId = card ? (card.dataset.hotelId || '') : '';
            populateBookingForm(hotelId, hotelName);
            var modal = gid('AccommodationBookingModal');
            if (modal) { modal.classList.add('active'); }
          }
        });
        var detailsModal = gid('AccommodationDetailsModal');
        if (detailsModal) detailsModal.addEventListener('click', function (e) { if (e.target === detailsModal) closeDetailsModal(); });
        var accModal = gid('AccommodationBookingModal');
        if (accModal) accModal.addEventListener('click', function (e) { if (e.target === accModal) closeModal(); });
      }

      function appendSavedSummary(hotelName, checkInIso, checkOutIso, priceDisplay, bookingId, bookingStatus) {
        var wrap = gid('AccommodationSummary');
        var body = gid('AccommodationSummaryBody');
        if (!wrap || !body) return;
        function esc(s) {
          return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
        }
        function fmtDate(iso) {
          if (!iso) return '';
          var d = new Date(String(iso).trim() + 'T12:00:00');
          if (isNaN(d.getTime())) return esc(iso);
          return esc(d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }));
        }
        var item = document.createElement('div');
        item.className = 'trip-accommodation-summary-item';
        if (bookingId != null && String(bookingId).trim() !== '') {
          item.setAttribute('data-booking-id', String(bookingId));
        }
        if (bookingStatus != null && String(bookingStatus).trim() !== '') {
          item.setAttribute('data-booking-status', String(bookingStatus).trim());
        }
        item.innerHTML =
          '<p><strong>Hotel:</strong> ' + esc(hotelName) + '</p>' +
          '<p><strong>Check-in:</strong> ' + fmtDate(checkInIso) + '</p>' +
          '<p><strong>Check-out:</strong> ' + fmtDate(checkOutIso) + '</p>' +
          '<p><strong>Total price:</strong> ' + esc(priceDisplay) + '</p>';
        body.appendChild(item);
        wrap.style.display = 'block';
      }
      var hotelBookingSubmitUrl = '/CeylonGo/public/tourist/hotel-request';
      var bookingFormEl = gid('AccommodationBookingForm');
      if (bookingFormEl) {
        bookingFormEl.addEventListener('submit', function (e) {
          e.preventDefault();
          if (!confirmBooking()) return;
          var fd = new FormData(bookingFormEl);
          var submitBtn = bookingFormEl.querySelector('button[type="submit"]');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
          }
          fetch(hotelBookingSubmitUrl, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
          })
            .then(function (r) {
              return r.text().then(function (text) {
                var data = null;
                try {
                  data = text ? JSON.parse(text) : null;
                } catch (err2) {
                  data = null;
                }
                return { ok: r.ok, data: data, raw: text };
              });
            })
            .then(function (res) {
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Confirm Booking';
              }
              if (res.data && res.data.success) {
                var sumName = res.data.hotel_name || (gid('AccommodationHotelName') || {}).value || '';
                var sumIn = res.data.check_in || (gid('AccommodationCheckIn') || {}).value || '';
                var sumOut = res.data.check_out || (gid('AccommodationCheckOut') || {}).value || '';
                var sumPrice = (res.data.total_price_display != null && String(res.data.total_price_display).trim() !== '')
                  ? String(res.data.total_price_display)
                  : ((gid('AccommodationTotalPrice') || {}).value || '');
                appendSavedSummary(sumName, sumIn, sumOut, sumPrice, res.data.booking_id, res.data.status);
                closeModal();
                var notice = gid('AccommodationBookingNotice');
                if (notice) {
                  notice.textContent = res.data.message || 'Your accommodation booking has been saved.';
                  notice.style.display = 'block';
                }
                bookingFormEl.reset();
                updateTotalPrice();
                return;
              }
              var errMsg = (res.data && res.data.error) ? res.data.error : 'Could not save booking.';
              if (!res.ok && !res.data) errMsg = 'Could not save booking. Please try again.';
              alert(errMsg);
            })
            .catch(function () {
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Confirm Booking';
              }
              alert('Network error. Please try again.');
            });
        });
      }
    }
    registerTripAccommodation('trip', 'start_date', 'end_date');
    registerTripAccommodation('trip2', 'start_date_2', 'end_date_2');
    registerTripAccommodation('trip3', 'start_date_3', 'end_date_3');

    (function initTripOverviewModals() {
      var budgetOverlay = document.getElementById('tripBudgetSummaryModalOverlay');
      var budgetClose = document.getElementById('tripBudgetSummaryModalClose');
      var budgetDownload = document.getElementById('tripBudgetSummaryModalDownload');
      if (budgetClose) budgetClose.addEventListener('click', closeTripBudgetSummaryModal);
      if (budgetDownload) {
        budgetDownload.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          downloadTripSummaryReport();
        });
      }
      if (budgetOverlay) {
        budgetOverlay.addEventListener('click', function (e) {
          if (e.target === budgetOverlay) closeTripBudgetSummaryModal();
        });
      }
      var tripRefundModal = document.getElementById('tripCustomRefundModal');
      if (tripRefundModal) {
        tripRefundModal.addEventListener('click', function (e) {
          var t = e.target;
          if (t && t.nodeType !== 1) t = t.parentElement;
          if (!t || !t.closest) return;
          if (t.closest('.js-trip-custom-refund-close')) {
            closeTripCustomRefundModal();
            return;
          }
          if (t.closest('.js-trip-custom-refund-step1-continue')) {
            e.preventDefault();
            showTripCustomRefundStep2();
            return;
          }
          if (t.closest('.js-trip-custom-refund-step1-cancel')) {
            closeTripCustomRefundModal();
            return;
          }
          if (t.closest('.js-trip-custom-refund-step2-back')) {
            showTripCustomRefundStep1();
            return;
          }
          if (t.closest('.js-trip-custom-refund-done')) {
            window.location.reload();
          }
        });
        tripRefundModal.addEventListener('submit', function (e) {
          var form = e.target;
          if (!form || form.id !== 'tripCustomRefundSubmitForm') return;
          e.preventDefault();
          var fd = new FormData(form);
          var submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn) submitBtn.disabled = true;
          fetch('/CeylonGo/public/tourist/trip/refund-request', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
          }).then(function (r) { return r.json(); }).then(function (data) {
            if (data && data.ok) {
              showTripCustomRefundSuccess(data.message || 'Your refund request has been submitted.');
              return;
            }
            alert(data && data.error ? data.error : 'Could not submit refund request.');
          }).catch(function () {
            alert('Could not submit refund request. Please try again.');
          }).then(function () {
            if (submitBtn) submitBtn.disabled = false;
          });
        });
      }
    })();

    (function autoOpenTripSummaryFromUrl() {
      try {
        if (window.__dashTripSummaryEmbedOpened) return;
        var params = new URLSearchParams(window.location.search || '');
        if (params.get('show_summary') !== '1') return;
        var tid = String(params.get('trip_id') || '').trim();
        if (!tid && typeof serverLastTid !== 'undefined' && serverLastTid > 0) tid = String(serverLastTid);
        setTimeout(function () { openTripBudgetSummaryModal(tid); }, 50);
      } catch (eAuto) {}
    })();

    (function tripWizardBootstrapAfterStops() {
      if (document.body.classList.contains('trip-summary-embed')) {
        try { scheduleSaveTripWizardDraft(); } catch (eEmb) {}
        return;
      }
      if (tripPageWizardFresh) {
        tripWizardPendingInitialStep = 1;
        tripWizardUrlForcedStep = true;
      } else {
        var draft = loadTripWizardDraft();
        if (draft && tripWizardDraftShouldRestore()) {
          try {
            applyTripWizardDraft(draft);
            if (!tripWizardUrlForcedStep && draft.step >= 1 && draft.step <= totalSteps) {
              tripWizardPendingInitialStep = clampWizardStepToAllowed(draft.step);
            }
          } catch (eApp) {
            try { console.warn('Trip wizard draft restore failed', eApp); } catch (eL) {}
          }
        }
      }
      var list1 = document.getElementById('tripStopsList');
      if (list1 && list1.querySelectorAll('.trip-stop-card').length === 0) {
        addStopCard();
      }
      showStep(tripWizardPendingInitialStep);
      updateLegStepperVisibility();
      updateAddStopsButtonLabel();
      try {
        var qsk = window.location.search || '';
        if (qsk.indexOf('afterPayment=1') !== -1 || qsk.indexOf('step=') !== -1 || qsk.indexOf('trip_id=') !== -1) {
          var u = new URL(window.location.href);
          u.searchParams.delete('afterPayment');
          u.searchParams.delete('step');
          u.searchParams.delete('trip_id');
          window.history.replaceState({}, document.title, u.pathname + u.search + u.hash);
        }
      } catch (e2) {}
      try { scheduleSaveTripWizardDraft(); } catch (e3) {}
    })();
  });
  </script>
</body>
</html>
