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
?>
<div class="trip-accommodation-content">
  <section class="hotel-search trip-accommodation-search">
    <div class="search-container">
      <h2 class="trip-accommodation-heading">Find Your Perfect Hotel</h2>
      <p>Discover amazing accommodations for your Sri Lankan adventure</p>
      <form class="search-form" onsubmit="return tripAccommodationSearch(event)">
        <input type="text" class="search-input" placeholder="Search by hotel name or location..." id="tripAccommodationSearchInput">
        <button type="submit" class="search-btn">Search Hotels</button>
      </form>
    </div>
  </section>

  <section class="hotels-container trip-accommodation-hotels">
    <div class="trip-accommodation-inner">
      <div class="filters">
        <div class="filter-row">
          <div class="filter-group">
            <label>Price Range</label>
            <select id="tripAccommodationPriceFilter">
              <option value="">All Prices</option>
              <option value="budget">Under Rs.50</option>
              <option value="mid">Rs.50 - Rs.150</option>
              <option value="luxury">Rs.150+</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Star Rating</label>
            <select id="tripAccommodationRatingFilter">
              <option value="">All Ratings</option>
              <option value="5">5 Stars</option>
              <option value="4">4 Stars</option>
              <option value="3">3 Stars</option>
              <option value="2">2 Stars</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Location</label>
            <select id="tripAccommodationLocationFilter">
              <option value="">All Locations</option>
              <option value="colombo">Colombo</option>
              <option value="kandy">Kandy</option>
              <option value="galle">Galle</option>
              <option value="nuwara">Nuwara Eliya</option>
            </select>
          </div>
        </div>
      </div>

      <div class="hotels-grid" id="tripAccommodationHotelsGrid">
        <div class="hotel-card" data-hotel-id="sunset-beach" data-price="luxury" data-rating="5" data-location="galle">
          <div class="hotel-image" style="background-image: url('<?php echo $img_base; ?>/5star.jpg')">
            <div class="hotel-badge">Luxury</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating"><div class="stars">★★★★★</div><span class="rating-text">5.0 (127 reviews)</span></div>
            <h3 class="hotel-name">Sunset Beach Resort</h3>
            <p class="hotel-location">📍 Galle, Southern Province</p>
            <div class="hotel-amenities"><span class="amenity">WiFi</span><span class="amenity">Pool</span><span class="amenity">Spa</span><span class="amenity">Restaurant</span></div>
            <div class="hotel-price"><div><span class="price">Rs.10,000</span><span class="price-period">/night</span></div></div>
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
            <div class="hotel-price"><div><span class="price">Rs.9,500</span><span class="price-period">/night</span></div></div>
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
            <div class="hotel-price"><div><span class="price">Rs.17,000</span><span class="price-period">/night</span></div></div>
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
            <div class="hotel-price"><div><span class="price">Rs.35,000</span><span class="price-period">/night</span></div></div>
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
            <div class="hotel-price"><div><span class="price">Rs.25,000</span><span class="price-period">/night</span></div></div>
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
            <div class="hotel-price"><div><span class="price">Rs.14,000</span><span class="price-period">/night</span></div></div>
            <div class="hotel-actions">
              <a href="#" class="btn-details" data-view-details>View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/backpackers-paradise" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div id="tripAccommodationDetailsModal" class="booking-modal trip-accommodation-modal trip-details-modal">
    <div class="modal-content modal-details-content">
      <div class="modal-header">
        <h2 id="tripDetailsModalTitle">Hotel Details</h2>
        <button type="button" class="close-modal" onclick="tripAccommodationCloseDetailsModal()" aria-label="Close">&times;</button>
      </div>
      <div class="modal-details-body">
        <div class="modal-details-image" id="tripDetailsModalImage"></div>
        <div class="modal-details-info">
          <div class="modal-details-rating" id="tripDetailsModalRating"></div>
          <h3 class="modal-details-name" id="tripDetailsModalName"></h3>
          <p class="modal-details-location" id="tripDetailsModalLocation"></p>
          <div class="modal-details-amenities" id="tripDetailsModalAmenities"></div>
          <div class="modal-details-price" id="tripDetailsModalPrice"></div>
          <div class="modal-details-rooms" id="tripDetailsModalRooms"></div>
          <button type="button" class="btn-book btn-book-from-details" onclick="tripAccommodationOpenBookingFromDetails()">Book Now</button>
        </div>
      </div>
    </div>
  </div>

  <div id="tripAccommodationBookingModal" class="booking-modal trip-accommodation-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Complete Your Booking</h2>
        <button type="button" class="close-modal" onclick="tripAccommodationCloseModal()" aria-label="Close">&times;</button>
      </div>
      <form method="POST" action="/CeylonGo/public/tourist/hotel-request" onsubmit="return tripAccommodationConfirmBooking();">
        <input type="hidden" id="tripAccommodationHotelName" name="hotel_name" value="">
        <input type="hidden" id="tripAccommodationHotelId" name="hotel_id" value="">
        <input type="hidden" id="tripAccommodationNights" name="nights" value="1">
        <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>">

        <div class="form-group">
          <label for="tripAccommodationCustomerName">Customer Name</label>
          <input type="text" id="tripAccommodationCustomerName" name="customer_name">
        </div>

        <div class="form-group">
          <label for="tripAccommodationContact">Contact Number</label>
          <input type="text" id="tripAccommodationContact" name="contact_number">
        </div>

        <div class="form-group">
          <label>Guests</label>
          <div class="trip-guests-row">
            <label class="trip-guests-total">
              Total
              <input type="number" id="tripAccommodationGuests" name="guests" min="1" max="100" value="1">
            </label>
            <label class="trip-guests-adults">
              Adults
              <input type="number" id="tripAccommodationAdults" name="adults" min="0" max="50" value="0">
            </label>
            <label class="trip-guests-children">
              Children
              <input type="number" id="tripAccommodationChildren" name="children" min="0" max="50" value="0">
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="tripAccommodationCheckIn">Check-in Date *</label>
          <input type="date" id="tripAccommodationCheckIn" name="check_in_date" required>
        </div>

        <div class="form-group">
          <label for="tripAccommodationCheckOut">Check-out Date *</label>
          <input type="date" id="tripAccommodationCheckOut" name="check_out_date" required>
        </div>

        <div class="form-group">
          <label for="tripAccommodationRoomType">Room Type *</label>
          <select id="tripAccommodationRoomType" name="room_type" required>
            <option value="">Select Room Type</option>
          </select>
        </div>

        <div class="form-group">
          <label for="tripAccommodationRoomCount">No. of Rooms</label>
          <input type="number" id="tripAccommodationRoomCount" name="room_count" value="1" min="1" max="10">
        </div>

        <div class="form-group">
          <label>Total Price</label>
          <input type="text" id="tripAccommodationTotalPrice" name="total_price_display" readonly value="Rs.0.00">
          <input type="hidden" id="tripAccommodationTotalPriceNumeric" name="total_price" value="0">
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="tripAccommodationCloseModal()">Cancel</button>
          <button type="submit" class="btn-confirm">Confirm Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  window.tripAccommodationRoomOptions = <?php echo json_encode($trip_hotel_room_options); ?>;
</script>
