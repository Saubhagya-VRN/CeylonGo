<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Tour Guide Dashboard</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/guide/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>
  
  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <ul>
        <li class="active"><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <div class="profile-box">
          <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
          <h5>Overview of your tour guide services</h5>
        </div>
      </div>

      <!-- Quick Stats Row -->
      <div class="quick-stats-row">
        <div class="stat-card trips">
          <div class="stat-icon">
            <i class="fa-solid fa-route"></i>
          </div>
          <div class="stat-content">
            <h3>25</h3>
            <p>Total Tours</p>
          </div>
        </div>

        <div class="stat-card customers">
          <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="stat-content">
            <h3>89</h3>
            <p>Tourists Guided</p>
          </div>
        </div>

        <div class="stat-card distance">
          <div class="stat-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div class="stat-content">
            <h3>8</h3>
            <p>Upcoming Tours</p>
          </div>
        </div>

        <div class="stat-card rating">
          <div class="stat-icon">
            <i class="fa-solid fa-star"></i>
          </div>
          <div class="stat-content">
            <h3>4.8</h3>
            <p>Average Rating</p>
          </div>
        </div>
      </div>

      <!-- Dashboard Widgets Container -->
      <div class="dashboard-widgets">
        <!-- Tour Calendar -->
        <div class="tour-calendar">
          <div class="calendar-top" id="selectedDate">
            Select a date
            <span class="icon icon-chevron-down"></span>
          </div>

          <div class="calendar-body">
            <div class="calendar-month">
              <button id="prevMonth"><i class="fa-solid fa-chevron-left"></i></button>
              <span id="monthYear"></span>
              <button id="nextMonth"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <div class="calendar-grid" id="calendarGrid"></div>
          </div>
        </div>

        <script>
        const calendarGrid = document.getElementById("calendarGrid");
        const monthYear = document.getElementById("monthYear");
        const selectedDateText = document.getElementById("selectedDate");

        let currentDate = new Date();

        // Helper function to check if a date is in the past
        function isPastDate(dateString) {
          const bookingDate = new Date(dateString);
          const today = new Date();
          today.setHours(0, 0, 0, 0);
          bookingDate.setHours(0, 0, 0, 0);
          return bookingDate < today;
        }

        // Tour Booking Data
        const allBookingData = [
          {
            date: "2026-01-20",
            status: "upcoming",
            customerName: "John Silva",
            tourType: "Historical Tour",
            time: "09:00 AM",
            location: "Sigiriya Rock Fortress",
            numPeople: 4,
            notes: "English speaking"
          },
          {
            date: "2026-01-25",
            status: "upcoming",
            customerName: "Sarah Fernando",
            tourType: "Cultural Tour",
            time: "08:00 AM",
            location: "Kandy Temple",
            numPeople: 6,
            notes: "Family group"
          },
          {
            date: "2026-02-01",
            status: "upcoming",
            customerName: "David Perera",
            tourType: "Beach Tour",
            time: "10:00 AM",
            location: "Galle Fort",
            numPeople: 3,
            notes: ""
          },
          {
            date: "2026-01-28",
            status: "cancelled",
            customerName: "Emma Rajapaksa",
            tourType: "Wildlife Tour",
            time: "06:00 AM",
            location: "Yala National Park",
            numPeople: 2,
            notes: "Cancelled due to weather"
          }
        ];

        const bookingData = allBookingData.filter(booking => !isPastDate(booking.date));

        function renderCalendar(date) {
          calendarGrid.innerHTML = "";

          const year = date.getFullYear();
          const month = date.getMonth();

          const monthNames = [
            "January","February","March","April","May","June",
            "July","August","September","October","November","December"
          ];

          monthYear.textContent = `${monthNames[month]} ${year}`;

          // Day headers
          ["Su","Mo","Tu","We","Th","Fr","Sa"].forEach(d => {
            const day = document.createElement("div");
            day.className = "day";
            day.textContent = d;
            calendarGrid.appendChild(day);
          });

          const firstDay = new Date(year, month, 1).getDay();
          const lastDate = new Date(year, month + 1, 0).getDate();
          const prevLastDate = new Date(year, month, 0).getDate();

          // Previous month dates
          for (let i = firstDay; i > 0; i--) {
            const d = document.createElement("div");
            d.className = "date muted";
            d.textContent = prevLastDate - i + 1;
            calendarGrid.appendChild(d);
          }

          // Current month dates
          for (let d = 1; d <= lastDate; d++) {
            const dateDiv = document.createElement("div");
            dateDiv.className = "date";
            dateDiv.textContent = d;

            const fullDate = `${year}-${String(month+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;

            // Check if this date has bookings
            const bookingsOnDate = bookingData.filter(b => b.date === fullDate);
            if (bookingsOnDate.length > 0) {
              const hasCancelled = bookingsOnDate.some(b => b.status === "cancelled");
              const hasUpcoming = bookingsOnDate.some(b => b.status === "upcoming");
              
              if (hasCancelled && hasUpcoming) {
                dateDiv.classList.add("booking-mixed");
              } else if (hasCancelled) {
                dateDiv.classList.add("booking-cancelled");
              } else {
                dateDiv.classList.add("booking-upcoming");
              }
              
              dateDiv.dataset.bookings = JSON.stringify(bookingsOnDate);
            }

            if (
              d === new Date().getDate() &&
              month === new Date().getMonth() &&
              year === new Date().getFullYear()
            ) {
              dateDiv.classList.add("selected");
            }

            dateDiv.onclick = () => {
              selectedDateText.innerHTML = `${monthNames[month]} ${d}, ${year}`;
              
              if (dateDiv.dataset.bookings) {
                showBookingDetails(JSON.parse(dateDiv.dataset.bookings), `${monthNames[month]} ${d}, ${year}`);
              }
            };

            calendarGrid.appendChild(dateDiv);
          }
        }

        // Navigation
        document.getElementById("prevMonth").onclick = () => {
          currentDate.setMonth(currentDate.getMonth() - 1);
          renderCalendar(currentDate);
        };

        document.getElementById("nextMonth").onclick = () => {
          currentDate.setMonth(currentDate.getMonth() + 1);
          renderCalendar(currentDate);
        };

        renderCalendar(currentDate);
        
        // Function to show booking details in modal
        function showBookingDetails(bookings, dateStr) {
          const modal = document.getElementById("bookingModal");
          const modalDate = document.getElementById("modalDate");
          const bookingList = document.getElementById("bookingList");
          
          modalDate.textContent = dateStr;
          bookingList.innerHTML = "";
          
          bookings.forEach((booking, index) => {
            const bookingCard = document.createElement("div");
            bookingCard.className = `booking-detail-card ${booking.status}`;
            bookingCard.innerHTML = `
              <div class="booking-header">
                <h4>${booking.customerName}</h4>
                <span class="status-badge ${booking.status}">${booking.status.toUpperCase()}</span>
              </div>
              <div class="booking-info">
                <p><i class="fa-solid fa-map-location-dot"></i> <strong>Tour:</strong> ${booking.tourType}</p>
                <p><i class="fa-regular fa-clock"></i> <strong>Time:</strong> ${booking.time}</p>
                <p><i class="fa-solid fa-location-dot"></i> <strong>Location:</strong> ${booking.location}</p>
                <p><i class="fa-solid fa-users"></i> <strong>Tourists:</strong> ${booking.numPeople}</p>
                ${booking.notes ? `<p><i class="fa-solid fa-note-sticky"></i> <strong>Notes:</strong> ${booking.notes}</p>` : ""}
              </div>
            `;
            bookingList.appendChild(bookingCard);
          });
          
          modal.style.display = "flex";
        }
        </script>

        <!-- Pending Booking Requests -->
        <div class="pending-requests-card">
          <div class="pending-requests-header">
            Pending Tour Requests
            <i class="fa-regular fa-clock"></i>
          </div>

          <div class="pending-requests-body" id="pendingRequestsBody">
            <!-- Pending requests will be dynamically populated -->
          </div>
          
          <script>
            // Populate pending bookings list
            function populatePendingBookings() {
              const pendingRequestsBody = document.getElementById('pendingRequestsBody');
              
              const pendingBookings = bookingData.filter(booking => 
                booking.status === 'upcoming' && !isPastDate(booking.date)
              );
              
              if (pendingBookings.length === 0) {
                pendingRequestsBody.innerHTML = '<p style="text-align: center; padding: 20px; color: #666;">No pending requests</p>';
                return;
              }
              
              pendingRequestsBody.innerHTML = '';
              
              pendingBookings.forEach((booking, index) => {
                const bookingDate = new Date(booking.date);
                const formattedDate = bookingDate.toLocaleDateString('en-US', { 
                  month: 'short', 
                  day: 'numeric', 
                  year: 'numeric' 
                });
                
                const requestCard = document.createElement('a');
                requestCard.href = `/CeylonGo/public/guide/pending#request-${index + 1}`;
                requestCard.className = 'pending-request-card';
                requestCard.innerHTML = `
                  <div class="request-header">
                    <i class="fa-solid fa-user"></i>
                    <h4>${booking.customerName}</h4>
                  </div>
                  <div class="request-details">
                    <p><i class="fa-solid fa-map-location-dot"></i> ${booking.tourType}</p>
                    <p><i class="fa-regular fa-calendar"></i> ${formattedDate}</p>
                    <p><i class="fa-solid fa-location-dot"></i> ${booking.location}</p>
                  </div>
                  <span class="request-badge">New</span>
                `;
                
                pendingRequestsBody.appendChild(requestCard);
              });
            }
            
            populatePendingBookings();
          </script>
        </div>
      </div>

      <!-- Booking Details Modal -->
      <div id="bookingModal" class="modal">
        <div class="modal-content">
          <span class="close" id="closeModal">&times;</span>
          <h2>Bookings for <span id="modalDate"></span></h2>
          <div id="bookingList" class="booking-list"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Modal close functionality
    document.addEventListener('DOMContentLoaded', function() {
      const closeBtn = document.getElementById("closeModal");
      const modal = document.getElementById("bookingModal");
      
      if (closeBtn) {
        closeBtn.onclick = function() {
          modal.style.display = "none";
        };
      }
      
      window.onclick = function(event) {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      };
    });
  </script>

  <!-- Hamburger Menu Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      
      function toggleSidebar() {
        hamburgerBtn.classList.toggle('active');
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      }
      
      function closeSidebar() {
        hamburgerBtn.classList.remove('active');
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
      
      if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
      }
      
      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
      }
    });
  </script>

  <!-- Profile Dropdown Script -->
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('show');
    }

    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profilePic = document.querySelector('.profile-pic');
      
      if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
        dropdown.classList.remove('show');
      }
    });
  </script>
  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

</body>
</html>
