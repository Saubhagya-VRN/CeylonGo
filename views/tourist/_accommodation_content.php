<?php
// Accommodation (choose hotel) content – included in trip.php (step 3) and optionally in choose_hotel.php.
// Uses /CeylonGo/public/ for assets so it works from trip page.
$img_base = '/CeylonGo/public/images';

// Room options per hotel (acts as simple data source for the details popup)
$trip_hotel_room_options = [
  'sunset-beach' => [
    [
      'type' => 'Standard Room',
      'description' => 'Cozy double room with garden view, ideal for couples.',
      'price' => 'Rs.12,500',
      'priceValue' => 12500,
      'image' => $img_base . '/5star.jpg',
    ],
    [
      'type' => 'Deluxe Sea View',
      'description' => 'Spacious room with balcony overlooking the ocean.',
      'price' => 'Rs.18,900',
      'priceValue' => 18900,
      'image' => $img_base . '/5star.jpg',
    ],
  ],
  'downtown-comfort' => [
    [
      'type' => 'Single Room',
      'description' => 'Comfortable single room for business travelers.',
      'price' => 'Rs.7,500',
      'priceValue' => 7500,
      'image' => $img_base . '/5star.jpg',
    ],
    [
      'type' => 'Deluxe Double',
      'description' => 'Modern double room with city view and workspace.',
      'price' => 'Rs.10,500',
      'priceValue' => 10500,
      'image' => $img_base . '/5star.jpg',
    ],
  ],
  'budget-stay' => [
    [
      'type' => 'Shared Dorm',
      'description' => 'Shared dormitory bed with locker access.',
      'price' => 'Rs.3,500',
      'priceValue' => 3500,
      'image' => $img_base . '/5star.jpg',
    ],
    [
      'type' => 'Private Room',
      'description' => 'Simple private room with shared facilities.',
      'price' => 'Rs.5,500',
      'priceValue' => 5500,
      'image' => $img_base . '/5star.jpg',
    ],
  ],
  'grand-ocean' => [
    [
      'type' => 'Deluxe Suite',
      'description' => 'Luxury suite with separate living area and lake view.',
      'price' => 'Rs.42,000',
      'priceValue' => 42000,
      'image' => $img_base . '/5star.jpg',
    ],
    [
      'type' => 'Family Suite',
      'description' => 'Two-bedroom suite perfect for families.',
      'price' => 'Rs.55,000',
      'priceValue' => 55000,
      'image' => $img_base . '/5star.jpg',
    ],
  ],
  'city-center' => [
    [
      'type' => 'Business Room',
      'description' => 'Room with ergonomic workspace and high-speed WiFi.',
      'price' => 'Rs.11,000',
      'priceValue' => 11000,
      'image' => $img_base . '/5star.jpg',
    ],
    [
      'type' => 'Executive Room',
      'description' => 'Larger room with lounge access and breakfast.',
      'price' => 'Rs.16,500',
      'priceValue' => 16500,
      'image' => $img_base . '/5star.jpg',
    ],
  ],
  'backpackers-paradise' => [
    [
      'type' => 'Mixed Dorm',
      'description' => 'Bunk bed in mixed dorm with A/C.',
      'price' => 'Rs.2,800',
      'priceValue' => 2800,
      'image' => $img_base . '/5star.jpg',
    ],
    [
      'type' => 'Female Dorm',
      'description' => 'Female-only dorm with ensuite bathroom.',
      'price' => 'Rs.3,200',
      'priceValue' => 3200,
      'image' => $img_base . '/5star.jpg',
    ],
  ],
];

// Card headline price = cheapest listed room (always matches one room in View Details)
$trip_hotel_card_price_label = [];
foreach ($trip_hotel_room_options as $hid => $rooms) {
    $minPv = null;
    $minLabel = 'Rs.0';
    foreach ($rooms as $r) {
        $pv = isset($r['priceValue']) ? (int) $r['priceValue'] : 0;
        if ($minPv === null || $pv < $minPv) {
            $minPv = $pv;
            $minLabel = isset($r['price']) ? $r['price'] : ('Rs.' . number_format($pv));
        }
    }
    $trip_hotel_card_price_label[$hid] = $minLabel;
}

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
        <div class="hotel-card" data-hotel-id="sunset-beach" data-price="luxury" data-rating="5" data-location="galle">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Luxury</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★★★</div><span class="rating-text">5.0 (127 reviews)</span></div>
            <h3 class="hotel-name">Sunset Beach Resort</h3>
            <p class="hotel-location">📍 Galle, Southern Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Pool</span><span class="amenity">Spa</span><span class="amenity">Restaurant</span></div>
            <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label['sunset-beach'] ?? 'Rs.0'); ?></span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/sunset-beach" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-hotel-id="downtown-comfort" data-price="mid" data-rating="4" data-location="colombo">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Popular</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★★☆</div><span class="rating-text">4.2 (89 reviews)</span></div>
            <h3 class="hotel-name">Downtown Comfort Inn</h3>
            <p class="hotel-location">📍 Colombo, Western Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Gym</span><span class="amenity">Restaurant</span><span class="amenity">Parking</span></div>
            <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label['downtown-comfort'] ?? 'Rs.0'); ?></span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/downtown-comfort" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-hotel-id="budget-stay" data-price="budget" data-rating="3" data-location="kandy">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Budget</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★☆☆</div><span class="rating-text">3.8 (45 reviews)</span></div>
            <h3 class="hotel-name">Budget Stay Hostel</h3>
            <p class="hotel-location">📍 Kandy, Central Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Shared Kitchen</span><span class="amenity">Laundry</span></div>
            <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label['budget-stay'] ?? 'Rs.0'); ?></span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/budget-stay" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-hotel-id="grand-ocean" data-price="luxury" data-rating="5" data-location="nuwara">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Luxury</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★★★</div><span class="rating-text">4.9 (156 reviews)</span></div>
            <h3 class="hotel-name">Grand Ocean Resort</h3>
            <p class="hotel-location">📍 Nuwara Eliya, Central Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Pool</span><span class="amenity">Spa</span><span class="amenity">Golf</span></div>
            <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label['grand-ocean'] ?? 'Rs.0'); ?></span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/grand-ocean" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-hotel-id="city-center" data-price="mid" data-rating="4" data-location="colombo">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Popular</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★★☆</div><span class="rating-text">4.1 (73 reviews)</span></div>
            <h3 class="hotel-name">City Center Hotel</h3>
            <p class="hotel-location">📍 Colombo, Western Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Restaurant</span><span class="amenity">Business Center</span><span class="amenity">Parking</span></div>
            <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label['city-center'] ?? 'Rs.0'); ?></span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/city-center" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-hotel-id="backpackers-paradise" data-price="budget" data-rating="3" data-location="galle">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Budget</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★☆☆</div><span class="rating-text">3.6 (32 reviews)</span></div>
            <h3 class="hotel-name">Backpacker's Paradise</h3>
            <p class="hotel-location">📍 Galle, Southern Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Shared Kitchen</span><span class="amenity">Tour Desk</span></div>
            <div class="hotel-price"><div><span class="price"><?php echo htmlspecialchars($trip_hotel_card_price_label['backpackers-paradise'] ?? 'Rs.0'); ?></span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/backpackers-paradise" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
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
