<?php
// choose_hotel.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Choose Hotel - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <style>
    .hotel-search {
      background: linear-gradient(135deg, rgba(44, 85, 48, 0.8), rgba(74, 124, 89, 0.8)), url('../../public/images/beach_hotel.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
      padding: 80px 20px;
      text-align: center;
    }
    
    .search-container {
      max-width: 600px;
      margin: 0 auto;
    }
    
    .search-form {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .search-input {
      flex: 1;
      min-width: 250px;
      padding: 15px 20px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .search-btn {
      padding: 15px 30px;
      background: #fff;
      color: #2c5530;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .search-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }
    
    .hotels-container {
      padding: 60px 20px;
      background: #f0f8f0;
    }
    
    .hotels-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .hotel-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      border: 1px solid rgba(74, 124, 89, 0.1);
      transition: all 0.3s ease;
    }
    
    .hotel-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(74, 124, 89, 0.25);
    }
    
    .hotel-image {
      height: 220px;
      background-size: cover;
      background-position: center;
      position: relative;
    }
    
    .hotel-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(44, 85, 48, 0.9);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .hotel-content {
      padding: 25px;
    }
    
    .hotel-rating {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 10px;
    }
    
    .stars {
      color: #f1b400;
      font-size: 16px;
    }
    
    .rating-text {
      color: #666;
      font-size: 14px;
    }
    
    .hotel-name {
      font-size: 1.5rem;
      font-weight: bold;
      color: #2c5530;
      margin-bottom: 8px;
    }
    
    .hotel-location {
      color: #666;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .hotel-amenities {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 20px;
    }
    
    .amenity {
      background: #f0f8f0;
      color: #2c5530;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 500;
    }
    
    .hotel-price {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .price {
      font-size: 1.5rem;
      font-weight: bold;
      color: #2c5530;
    }
    
    .price-period {
      color: #666;
      font-size: 14px;
    }
    
    .hotel-actions {
      display: flex;
      gap: 12px;
    }
    
    .btn-details {
      flex: 1;
      padding: 12px 20px;
      background: #fff;
      color: #2c5530;
      border: 2px solid #2c5530;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-details:hover {
      background: #2c5530;
      color: #fff;
    }
    
    .btn-book {
      flex: 1;
      padding: 12px 20px;
      background: #2c5530;
      color: #fff;
      border: 2px solid #2c5530;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-book:hover {
      background: #4a7c59;
      border-color: #4a7c59;
      transform: translateY(-1px);
    }
    
    .filters {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(74, 124, 89, 0.1);
    }
    
    .filter-row {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      align-items: center;
    }
    
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    
    .filter-group label {
      font-weight: 600;
      color: #2c5530;
      font-size: 14px;
    }
    
    .filter-group select {
      padding: 10px 15px;
      border: 2px solid #e0e8e0;
      border-radius: 8px;
      background: #fff;
      color: #333;
      font-size: 14px;
    }
    
    .filter-group select:focus {
      outline: none;
      border-color: #4a7c59;
    }
    
    @media (max-width: 768px) {
      .search-form {
        flex-direction: column;
        align-items: center;
      }
      
      .search-input {
        width: 100%;
        max-width: 300px;
      }
      
      .hotels-grid {
        grid-template-columns: 1fr;
      }
      
      .filter-row {
        flex-direction: column;
        align-items: stretch;
      }
      
      .hotel-actions {
        flex-direction: column;
      }
    }
  </style>
</head>
<body class="bg-app">
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Hotel Search Section -->
  <section class="hotel-search">
    <div class="search-container">
      <h1>Find Your Perfect Hotel</h1>
      <p>Discover amazing accommodations for your Sri Lankan adventure</p>
      
      <form class="search-form" onsubmit="searchHotels(event)">
        <input type="text" class="search-input" placeholder="Search by hotel name or location..." id="searchInput">
        <button type="submit" class="search-btn">Search Hotels</button>
      </form>
    </div>
  </section>

  <!-- Hotels Section -->
  <section class="hotels-container">
    <div style="max-width: 1200px; margin: 0 auto;">
      <!-- Filters -->
      <div class="filters">
        <div class="filter-row">
          <div class="filter-group">
            <label>Price Range</label>
            <select id="priceFilter">
              <option value="">All Prices</option>
              <option value="budget">Under Rs.50</option>
              <option value="mid">Rs.50 - Rs.150</option>
              <option value="luxury">Rs.150+</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Star Rating</label>
            <select id="ratingFilter">
              <option value="">All Ratings</option>
              <option value="5">5 Stars</option>
              <option value="4">4 Stars</option>
              <option value="3">3 Stars</option>
              <option value="2">2 Stars</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Location</label>
            <select id="locationFilter">
              <option value="">All Locations</option>
              <option value="colombo">Colombo</option>
              <option value="kandy">Kandy</option>
              <option value="galle">Galle</option>
              <option value="nuwara">Nuwara Eliya</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Hotels Grid -->
      <div class="hotels-grid" id="hotelsGrid">
        <!-- Hotel 1 -->
        <div class="hotel-card" data-price="luxury" data-rating="5" data-location="galle">
          <div class="hotel-image" style="background-image: url('../../public/images/5star.jpg')">
            <div class="hotel-badge">Luxury</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating">
              <div class="stars">★★★★★</div>
              <span class="rating-text">5.0 (127 reviews)</span>
            </div>
            <h3 class="hotel-name">Sunset Beach Resort</h3>
            <p class="hotel-location">📍 Galle, Southern Province</p>
            <div class="hotel-amenities">
              <span class="amenity">WiFi</span>
              <span class="amenity">Pool</span>
              <span class="amenity">Spa</span>
              <span class="amenity">Restaurant</span>
            </div>
            <div class="hotel-price">
              <div>
                <span class="price">Rs.10,000</span>
                <span class="price-period">/night</span>
              </div>
            </div>
            <div class="hotel-actions">
              <button class="btn-details" onclick="viewDetails('Sunset Beach Resort')">View Details</button>
              <button class="btn-book" onclick="bookHotel('Sunset Beach Resort')">Book Now</button>
            </div>
          </div>
        </div>

        <!-- Hotel 2 -->
        <div class="hotel-card" data-price="mid" data-rating="4" data-location="colombo">
          <div class="hotel-image" style="background-image: url('../../public/images/5star.jpg')">
            <div class="hotel-badge">Popular</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating">
              <div class="stars">★★★★☆</div>
              <span class="rating-text">4.2 (89 reviews)</span>
            </div>
            <h3 class="hotel-name">Downtown Comfort Inn</h3>
            <p class="hotel-location">📍 Colombo, Western Province</p>
            <div class="hotel-amenities">
              <span class="amenity">WiFi</span>
              <span class="amenity">Gym</span>
              <span class="amenity">Restaurant</span>
              <span class="amenity">Parking</span>
            </div>
            <div class="hotel-price">
              <div>
                <span class="price">Rs.9500</span>
                <span class="price-period">/night</span>
              </div>
            </div>
            <div class="hotel-actions">
              <button class="btn-details" onclick="viewDetails('Downtown Comfort Inn')">View Details</button>
              <button class="btn-book" onclick="bookHotel('Downtown Comfort Inn')">Book Now</button>
            </div>
          </div>
        </div>

        <!-- Hotel 3 -->
        <div class="hotel-card" data-price="budget" data-rating="3" data-location="kandy">
          <div class="hotel-image" style="background-image: url('../../public/images/5star.jpg')">
            <div class="hotel-badge">Budget</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating">
              <div class="stars">★★★☆☆</div>
              <span class="rating-text">3.8 (45 reviews)</span>
            </div>
            <h3 class="hotel-name">Budget Stay Hostel</h3>
            <p class="hotel-location">📍 Kandy, Central Province</p>
            <div class="hotel-amenities">
              <span class="amenity">WiFi</span>
              <span class="amenity">Shared Kitchen</span>
              <span class="amenity">Laundry</span>
            </div>
            <div class="hotel-price">
              <div>
                <span class="price">Rs.17,000</span>
                <span class="price-period">/night</span>
              </div>
            </div>
            <div class="hotel-actions">
              <button class="btn-details" onclick="viewDetails('Budget Stay Hostel')">View Details</button>
              <button class="btn-book" onclick="bookHotel('Budget Stay Hostel')">Book Now</button>
            </div>
          </div>
        </div>

        <!-- Hotel 4 -->
        <div class="hotel-card" data-price="luxury" data-rating="5" data-location="nuwara">
          <div class="hotel-image" style="background-image: url('../../public/images/5star.jpg')">
            <div class="hotel-badge">Luxury</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating">
              <div class="stars">★★★★★</div>
              <span class="rating-text">4.9 (156 reviews)</span>
            </div>
            <h3 class="hotel-name">Grand Ocean Resort</h3>
            <p class="hotel-location">📍 Nuwara Eliya, Central Province</p>
            <div class="hotel-amenities">
              <span class="amenity">WiFi</span>
              <span class="amenity">Pool</span>
              <span class="amenity">Spa</span>
              <span class="amenity">Golf</span>
            </div>
            <div class="hotel-price">
              <div>
                <span class="price">Rs.35,000</span>
                <span class="price-period">/night</span>
              </div>
            </div>
            <div class="hotel-actions">
              <button class="btn-details" onclick="viewDetails('Grand Ocean Resort')">View Details</button>
              <button class="btn-book" onclick="bookHotel('Grand Ocean Resort')">Book Now</button>
            </div>
          </div>
        </div>

        <!-- Hotel 5 -->
        <div class="hotel-card" data-price="mid" data-rating="4" data-location="colombo">
          <div class="hotel-image" style="background-image: url('../../public/images/5star.jpg')">
            <div class="hotel-badge">Popular</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating">
              <div class="stars">★★★★☆</div>
              <span class="rating-text">4.1 (73 reviews)</span>
            </div>
            <h3 class="hotel-name">City Center Hotel</h3>
            <p class="hotel-location">📍 Colombo, Western Province</p>
            <div class="hotel-amenities">
              <span class="amenity">WiFi</span>
              <span class="amenity">Restaurant</span>
              <span class="amenity">Business Center</span>
              <span class="amenity">Parking</span>
            </div>
            <div class="hotel-price">
              <div>
                <span class="price">Rs.25,000</span>
                <span class="price-period">/night</span>
              </div>
            </div>
            <div class="hotel-actions">
              <button class="btn-details" onclick="viewDetails('City Center Hotel')">View Details</button>
              <button class="btn-book" onclick="bookHotel('City Center Hotel')">Book Now</button>
            </div>
          </div>
        </div>

        <!-- Hotel 6 -->
        <div class="hotel-card" data-price="budget" data-rating="3" data-location="galle">
          <div class="hotel-image" style="background-image: url('../../public/images/5star.jpg')">
            <div class="hotel-badge">Budget</div>
          </div>
          <div class="hotel-content">
            <div class="hotel-rating">
              <div class="stars">★★★☆☆</div>
              <span class="rating-text">3.6 (32 reviews)</span>
            </div>
            <h3 class="hotel-name">Backpacker's Paradise</h3>
            <p class="hotel-location">📍 Galle, Southern Province</p>
            <div class="hotel-amenities">
              <span class="amenity">WiFi</span>
              <span class="amenity">Shared Kitchen</span>
              <span class="amenity">Tour Desk</span>
            </div>
            <div class="hotel-price">
              <div>
                <span class="price">Rs.14,000</span>
                <span class="price-period">/night</span>
              </div>
            </div>
            <div class="hotel-actions">
              <button class="btn-details" onclick="viewDetails('Backpacker\'s Paradise')">View Details</button>
              <button class="btn-book" onclick="bookHotel('Backpacker\'s Paradise')">Book Now</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script>
    // Search functionality
    function searchHotels(event) {
      event.preventDefault();
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const hotelCards = document.querySelectorAll('.hotel-card');
      
      hotelCards.forEach(card => {
        const hotelName = card.querySelector('.hotel-name').textContent.toLowerCase();
        const hotelLocation = card.querySelector('.hotel-location').textContent.toLowerCase();
        
        if (hotelName.includes(searchTerm) || hotelLocation.includes(searchTerm)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Filter functionality
    function applyFilters() {
      const priceFilter = document.getElementById('priceFilter').value;
      const ratingFilter = document.getElementById('ratingFilter').value;
      const locationFilter = document.getElementById('locationFilter').value;
      const hotelCards = document.querySelectorAll('.hotel-card');
      
      hotelCards.forEach(card => {
        let show = true;
        
        if (priceFilter && card.dataset.price !== priceFilter) {
          show = false;
        }
        
        if (ratingFilter && card.dataset.rating !== ratingFilter) {
          show = false;
        }
        
        if (locationFilter && !card.dataset.location.includes(locationFilter)) {
          show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
      });
    }

    // View hotel details
    function viewDetails(hotelName) {
      alert(`🏨 ${hotelName}\n\n✨ Features:\n• Free WiFi\n• Swimming Pool\n• Restaurant\n• Spa Services\n• 24/7 Room Service\n• Airport Shuttle\n\n📍 Location: Prime location with easy access to attractions\n\n💰 Best Price Guarantee\n\nThis would typically open a detailed modal or new page with complete hotel information, photos, reviews, and booking options.`);
    }

    // Book hotel
    function bookHotel(hotelName) {
      if (confirm(`🏨 Ready to book ${hotelName}?\n\nThis will redirect you to the booking form where you can:\n• Select dates\n• Choose room type\n• Add special requests\n• Complete payment\n\nContinue to booking?`)) {
        window.location.href = `booking_form?hotel=${encodeURIComponent(hotelName)}`;
      }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Filter event listeners
      document.getElementById('priceFilter').addEventListener('change', applyFilters);
      document.getElementById('ratingFilter').addEventListener('change', applyFilters);
      document.getElementById('locationFilter').addEventListener('change', applyFilters);
      
      // Search input event listener
      document.getElementById('searchInput').addEventListener('input', function() {
        if (this.value === '') {
          // Show all cards when search is cleared
          document.querySelectorAll('.hotel-card').forEach(card => {
            card.style.display = 'block';
          });
        }
      });
    });
  </script>

</body>
</html>
