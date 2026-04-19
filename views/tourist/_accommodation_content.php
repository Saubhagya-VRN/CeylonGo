<?php
// Accommodation (choose hotel) content – included in trip.php (step 3) and optionally in choose_hotel.php.
// Uses /CeylonGo/public/ for assets so it works from trip page.
$img_base = '/CeylonGo/public/images';
$hotels = isset($hotels) && is_array($hotels) ? $hotels : [];
$trip_hotels_by_slug = [];
$trip_hotel_room_options = [];
$trip_hotel_card_price_label = [];
$trip_hotel_room_seen = [];

foreach ($hotels as $hotel_row) {
    if (!is_array($hotel_row)) {
        continue;
    }

    $hotel_slug = trim((string)($hotel_row['hotel_slug'] ?? ''));
    if ($hotel_slug === '') {
        continue;
    }

    if (!isset($trip_hotels_by_slug[$hotel_slug])) {
        $trip_hotels_by_slug[$hotel_slug] = $hotel_row;
    }

    if (!isset($trip_hotel_room_options[$hotel_slug])) {
        $trip_hotel_room_options[$hotel_slug] = [];
    }
    if (!isset($trip_hotel_room_seen[$hotel_slug])) {
        $trip_hotel_room_seen[$hotel_slug] = [];
    }

    $room_details_raw = $hotel_row['room_details'] ?? [];
    $room_details = [];
    if (is_string($room_details_raw)) {
        $decoded_room_details = json_decode($room_details_raw, true);
        if (is_array($decoded_room_details)) {
            $room_details = $decoded_room_details;
        }
    } elseif (is_array($room_details_raw)) {
        $room_details = $room_details_raw;
    }

    foreach ($room_details as $room) {
        if (!is_array($room)) {
            continue;
        }

        $room_price_value = isset($room['priceValue']) ? (int)$room['priceValue'] : 0;
        if ($room_price_value <= 0 && isset($room['price'])) {
            $room_price_value = (int)preg_replace('/[^0-9]/', '', (string)$room['price']);
        }

        $room_type = trim((string)($room['type'] ?? 'Room'));
        $room_desc = trim((string)($room['description'] ?? ''));
        $room_price_label = trim((string)($room['price'] ?? ''));
        if ($room_price_label === '') {
            $room_price_label = 'Rs.' . number_format($room_price_value);
        }

        $room_image = trim((string)($room['image'] ?? ''));
        if ($room_image !== '') {
          if (preg_match('#^/img/#i', $room_image)) {
            $room_image = $img_base . '/' . ltrim(substr($room_image, 5), '/');
          }
        }
        if ($room_image === '') {
            $room_image = trim((string)($hotel_row['hero_image'] ?? ''));
        }
        if ($room_image === '') {
            $room_image = $img_base . '/5star.jpg';
        }

        $room_unique_key = md5($room_type . '|' . $room_desc . '|' . $room_price_value . '|' . $room_price_label);
        if (isset($trip_hotel_room_seen[$hotel_slug][$room_unique_key])) {
            continue;
        }

        $trip_hotel_room_seen[$hotel_slug][$room_unique_key] = true;
        $trip_hotel_room_options[$hotel_slug][] = [
            'type' => $room_type,
            'description' => $room_desc,
            'price' => $room_price_label,
            'priceValue' => $room_price_value,
            'image' => $room_image,
        ];
    }
}

foreach ($trip_hotels_by_slug as $hotel_slug => $hotel_row) {
    $from_price = isset($hotel_row['from_price']) ? (float)$hotel_row['from_price'] : 0;
    $card_price_label = $from_price > 0 ? ('Rs.' . number_format($from_price)) : 'Rs.0';

    if ($from_price <= 0 && isset($trip_hotel_room_options[$hotel_slug])) {
        $min_room_price = null;
        foreach ($trip_hotel_room_options[$hotel_slug] as $room) {
            $room_price_value = isset($room['priceValue']) ? (int)$room['priceValue'] : 0;
            if ($min_room_price === null || ($room_price_value > 0 && $room_price_value < $min_room_price)) {
                $min_room_price = $room_price_value;
                $card_price_label = isset($room['price']) ? (string)$room['price'] : ('Rs.' . number_format($room_price_value));
            }
        }
    }

    $trip_hotel_card_price_label[$hotel_slug] = $card_price_label;
}

$trip_hotels_list = array_values($trip_hotels_by_slug);
usort($trip_hotels_list, function ($a, $b) {
    $a_sort = isset($a['sort_order']) ? (int)$a['sort_order'] : PHP_INT_MAX;
    $b_sort = isset($b['sort_order']) ? (int)$b['sort_order'] : PHP_INT_MAX;
    if ($a_sort === $b_sort) {
        return strcmp((string)($a['hotel_name'] ?? ''), (string)($b['hotel_name'] ?? ''));
    }
    return $a_sort <=> $b_sort;
});

$trip_accommodation_block = isset($trip_accommodation_block) ? $trip_accommodation_block : 'primary';
if ($trip_accommodation_block === 'secondary') {
    $p = 'trip2';
} elseif ($trip_accommodation_block === 'tertiary') {
    $p = 'trip3';
} else {
    $p = 'trip';
}
?>
<div class="trip-accommodation-content<?php
  echo $trip_accommodation_block === 'secondary' ? ' trip-accommodation-content--secondary' : '';
  echo $trip_accommodation_block === 'tertiary' ? ' trip-accommodation-content--tertiary' : '';
?>">
  <section class="hotel-search trip-accommodation-search">
    <div class="search-container">
      <h2 class="trip-accommodation-heading">Find Your Perfect Hotel</h2>
      <p>Discover amazing accommodations for your Sri Lankan adventure</p>
      <form class="search-form" onsubmit="return <?php echo $p; ?>AccommodationSearch(event)">
        <input type="text" class="search-input" placeholder="Search by hotel name or location..." id="<?php echo $p; ?>AccommodationSearchInput">
        <button type="submit" class="search-btn">Search Hotels</button>
      </form>
    </div>
  </section>

  <section class="hotels-container trip-accommodation-hotels">
    <div class="trip-accommodation-inner">
      <p id="<?php echo $p; ?>AccommodationBookingNotice" class="trip-accommodation-booking-notice" style="display:none" role="status" aria-live="polite"></p>
      <div class="filters">
        <div class="filter-row">
          <div class="filter-group">
            <label>Price Range</label>
            <select id="<?php echo $p; ?>AccommodationPriceFilter">
              <option value="">All Prices</option>
              <option value="budget">Under Rs.50</option>
              <option value="mid">Rs.50 - Rs.150</option>
              <option value="luxury">Rs.150+</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Star Rating</label>
            <select id="<?php echo $p; ?>AccommodationRatingFilter">
              <option value="">All Ratings</option>
              <option value="5">5 Stars</option>
              <option value="4">4 Stars</option>
              <option value="3">3 Stars</option>
              <option value="2">2 Stars</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Location</label>
            <select id="<?php echo $p; ?>AccommodationLocationFilter">
              <option value="">All Locations</option>
              <option value="colombo">Colombo</option>
              <option value="kandy">Kandy</option>
              <option value="galle">Galle</option>
              <option value="nuwara">Nuwara Eliya</option>
            </select>
          </div>
        </div>
      </div>

      <div class="hotels-grid" id="<?php echo $p; ?>AccommodationHotelsGrid">
        <?php foreach ($trip_hotels_list as $hotel): ?>
          <?php
            $slug = trim((string)($hotel['hotel_slug'] ?? ''));
            if ($slug === '') {
                continue;
            }

            $hotel_name = trim((string)($hotel['hotel_name'] ?? 'Unnamed Hotel'));
            $location = trim((string)($hotel['location'] ?? ''));
            $location_filter_value = strtolower($location);
            $rating_value = isset($hotel['rating']) ? (float)$hotel['rating'] : 0;
            $rating_bucket = (string)max(1, min(5, (int)round($rating_value)));
            $review_count = isset($hotel['review_count']) ? (int)$hotel['review_count'] : 0;

            $amenities_raw = $hotel['amenities'] ?? [];
            $amenities = [];
            if (is_string($amenities_raw)) {
                $decoded_amenities = json_decode($amenities_raw, true);
                if (is_array($decoded_amenities)) {
                    $amenities = $decoded_amenities;
                }
            } elseif (is_array($amenities_raw)) {
                $amenities = $amenities_raw;
            }

            $hero_image = trim((string)($hotel['hero_image'] ?? ''));
            if ($hero_image === '') {
                $hero_image = $img_base . '/5star.jpg';
            }

            $from_price = isset($hotel['from_price']) ? (float)$hotel['from_price'] : 0;
            if ($from_price <= 0 && isset($trip_hotel_room_options[$slug])) {
                foreach ($trip_hotel_room_options[$slug] as $room) {
                    $room_price_value = isset($room['priceValue']) ? (int)$room['priceValue'] : 0;
                    if ($room_price_value > 0 && ($from_price <= 0 || $room_price_value < $from_price)) {
                        $from_price = $room_price_value;
                    }
                }
            }

            if ($from_price > 0 && $from_price < 7000) {
                $price_bucket = 'budget';
            } elseif ($from_price > 0 && $from_price < 12000) {
                $price_bucket = 'mid';
            } else {
                $price_bucket = 'luxury';
            }

            $badge_label = $price_bucket === 'budget' ? 'Budget' : ($price_bucket === 'mid' ? 'Popular' : 'Luxury');
            $stars_text = str_repeat('★', (int)$rating_bucket) . str_repeat('☆', 5 - (int)$rating_bucket);
          ?>
          <div class="hotel-card" data-hotel-id="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>" data-price="<?php echo $price_bucket; ?>" data-rating="<?php echo $rating_bucket; ?>" data-location="<?php echo htmlspecialchars($location_filter_value, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="hotel-image" style="background-image: url('<?php echo htmlspecialchars($hero_image, ENT_QUOTES, 'UTF-8'); ?>')">
              <div class="hotel-badge"><?php echo $badge_label; ?></div>
            </div>
            <div class="hotel-content">
              <div class="hotel-rating"><div class="stars"><?php echo $stars_text; ?></div><span class="rating-text"><?php echo number_format($rating_value, 1); ?> (<?php echo $review_count; ?> reviews)</span></div>
              <h3 class="hotel-name"><?php echo htmlspecialchars($hotel_name, ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="hotel-location">📍 <?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?></p>
              <div class="hotel-amenities">
                <?php if (!empty($amenities)): ?>
                  <?php foreach ($amenities as $amenity): ?>
                    <span class="amenity"><?php echo htmlspecialchars((string)$amenity, ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label[$slug] ?? 'Rs.0', ENT_QUOTES, 'UTF-8'); ?></span><span class="price-period">/night</span></div></div>
              <div class="hotel-actions">
                <a href="#" class="btn-details" data-view-details>View Details</a>
                <a href="/CeylonGo/public/tourist/hotel-details/<?php echo rawurlencode($slug); ?>" class="btn-book">Book Now</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div id="<?php echo $p; ?>AccommodationSummary" class="trip-accommodation-summary" style="display:none" aria-live="polite">
        <p class="trip-accommodation-summary-label">Accommodation bookings</p>
        <div id="<?php echo $p; ?>AccommodationSummaryBody" class="trip-accommodation-summary-body"></div>
      </div>
    </div>
  </section>

  <div id="<?php echo $p; ?>AccommodationDetailsModal" class="booking-modal trip-accommodation-modal trip-details-modal">
    <div class="modal-content modal-details-content">
      <div class="modal-header">
        <h2 id="<?php echo $p; ?>DetailsModalTitle">Hotel Details</h2>
        <button type="button" class="close-modal" onclick="<?php echo $p; ?>AccommodationCloseDetailsModal()" aria-label="Close">&times;</button>
      </div>
      <div class="modal-details-body">
        <div class="modal-details-image" id="<?php echo $p; ?>DetailsModalImage"></div>
        <div class="modal-details-info">
          <div class="modal-details-rating" id="<?php echo $p; ?>DetailsModalRating"></div>
          <h3 class="modal-details-name" id="<?php echo $p; ?>DetailsModalName"></h3>
          <p class="modal-details-location" id="<?php echo $p; ?>DetailsModalLocation"></p>
          <div class="modal-details-amenities" id="<?php echo $p; ?>DetailsModalAmenities"></div>
          <div class="modal-details-price" id="<?php echo $p; ?>DetailsModalPrice"></div>
          <div class="modal-details-rooms" id="<?php echo $p; ?>DetailsModalRooms"></div>
          <button type="button" class="btn-book btn-book-from-details" onclick="<?php echo $p; ?>AccommodationOpenBookingFromDetails()">Book Now</button>
        </div>
      </div>
    </div>
  </div>

  <div id="<?php echo $p; ?>AccommodationBookingModal" class="booking-modal trip-accommodation-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Complete Your Booking</h2>
        <button type="button" class="close-modal" onclick="<?php echo $p; ?>AccommodationCloseModal()" aria-label="Close">&times;</button>
      </div>
      <form method="POST" action="/CeylonGo/public/tourist/hotel-request" id="<?php echo $p; ?>AccommodationBookingForm" novalidate>
        <input type="hidden" id="<?php echo $p; ?>AccommodationHotelName" name="hotel_name" value="">
        <input type="hidden" id="<?php echo $p; ?>AccommodationHotelId" name="hotel_id" value="">
        <input type="hidden" id="<?php echo $p; ?>AccommodationNights" name="nights" value="1">
        <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>">
        <input type="hidden" id="<?php echo $p; ?>AccommodationBookingStatus" name="booking_status" value="">

        <div class="form-group">
          <label for="<?php echo $p; ?>AccommodationCustomerName">Customer Name</label>
          <input type="text" id="<?php echo $p; ?>AccommodationCustomerName" name="customer_name">
        </div>

        <div class="form-group">
          <label for="<?php echo $p; ?>AccommodationContact">Contact Number</label>
          <input type="text" id="<?php echo $p; ?>AccommodationContact" name="contact_number">
        </div>

        <div class="form-group">
          <label>Guests</label>
          <div class="trip-guests-row">
            <label class="trip-guests-total">
              Total
              <input type="number" id="<?php echo $p; ?>AccommodationGuests" name="guests" min="1" max="100" value="1">
            </label>
            <label class="trip-guests-adults">
              Adults
              <input type="number" id="<?php echo $p; ?>AccommodationAdults" name="adults" min="0" max="50" value="0">
            </label>
            <label class="trip-guests-children">
              Children
              <input type="number" id="<?php echo $p; ?>AccommodationChildren" name="children" min="0" max="50" value="0">
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="<?php echo $p; ?>AccommodationCheckIn">Check-in Date *</label>
          <input type="date" id="<?php echo $p; ?>AccommodationCheckIn" name="check_in_date" required>
        </div>

        <div class="form-group">
          <label for="<?php echo $p; ?>AccommodationCheckOut">Check-out Date *</label>
          <input type="date" id="<?php echo $p; ?>AccommodationCheckOut" name="check_out_date" required>
        </div>

        <div class="form-group">
          <label for="<?php echo $p; ?>AccommodationRoomType">Room Type *</label>
          <select id="<?php echo $p; ?>AccommodationRoomType" name="room_type" required>
            <option value="">Select Room Type</option>
          </select>
        </div>

        <div class="form-group">
          <label for="<?php echo $p; ?>AccommodationRoomCount">No. of Rooms</label>
          <input type="number" id="<?php echo $p; ?>AccommodationRoomCount" name="room_count" value="1" min="1" max="10">
        </div>

        <div class="form-group">
          <label>Total Price</label>
          <input type="text" id="<?php echo $p; ?>AccommodationTotalPrice" name="total_price_display" readonly value="Rs.0.00">
          <input type="hidden" id="<?php echo $p; ?>AccommodationTotalPriceNumeric" name="total_price" value="0">
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="<?php echo $p; ?>AccommodationCloseModal()">Cancel</button>
          <button type="submit" class="btn-confirm">Confirm Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  window['<?php echo $p; ?>AccommodationRoomOptions'] = <?php echo json_encode($trip_hotel_room_options); ?>;
</script>
