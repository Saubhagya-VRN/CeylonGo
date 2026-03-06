<?php
// Accommodation (choose hotel) content – included in trip.php (step 3) and optionally in choose_hotel.php.
// Uses /CeylonGo/public/ for assets so it works from trip page.
$img_base = '/CeylonGo/public/images';
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
        <div class="hotel-card" data-price="luxury" data-rating="5" data-location="galle">
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
              <a href="/CeylonGo/public/tourist/hotel-details/sunset-beach" class="btn-details">View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/sunset-beach" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-price="mid" data-rating="4" data-location="colombo">
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
              <a href="/CeylonGo/public/tourist/hotel-details/downtown-comfort" class="btn-details">View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/downtown-comfort" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-price="budget" data-rating="3" data-location="kandy">
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
              <a href="/CeylonGo/public/tourist/hotel-details/budget-stay" class="btn-details">View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/budget-stay" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-price="luxury" data-rating="5" data-location="nuwara">
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
              <a href="/CeylonGo/public/tourist/hotel-details/grand-ocean" class="btn-details">View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/grand-ocean" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-price="mid" data-rating="4" data-location="colombo">
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
              <a href="/CeylonGo/public/tourist/hotel-details/city-center" class="btn-details">View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/city-center" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
        <div class="hotel-card" data-price="budget" data-rating="3" data-location="galle">
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
              <a href="/CeylonGo/public/tourist/hotel-details/backpackers-paradise" class="btn-details">View Details</a>
              <a href="/CeylonGo/public/tourist/hotel-details/backpackers-paradise" class="btn-book">Book Now</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div id="tripAccommodationBookingModal" class="booking-modal trip-accommodation-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Complete Your Booking</h2>
        <button type="button" class="close-modal" onclick="tripAccommodationCloseModal()" aria-label="Close">&times;</button>
      </div>
      <form onsubmit="tripAccommodationConfirmBooking(); return false;">
        <input type="hidden" id="tripAccommodationHotelName" value="">
        <div class="form-group">
          <label for="tripAccommodationCheckIn">Check-in Date *</label>
          <input type="date" id="tripAccommodationCheckIn" name="checkInDate" required>
        </div>
        <div class="form-group">
          <label for="tripAccommodationRoomType">Room Type *</label>
          <select id="tripAccommodationRoomType" name="roomType" required>
            <option value="">Select Room Type</option>
            <option value="Single">Single Room</option>
            <option value="Double">Double Room</option>
            <option value="Twin">Twin Room</option>
            <option value="Triple">Triple Room</option>
            <option value="Suite">Suite</option>
            <option value="Deluxe">Deluxe Room</option>
            <option value="Family">Family Room</option>
          </select>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="tripAccommodationCloseModal()">Cancel</button>
          <button type="submit" class="btn-confirm">Confirm Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>
