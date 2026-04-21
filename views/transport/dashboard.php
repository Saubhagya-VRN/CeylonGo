<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Dashboard</title>
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/tables.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/profile.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/charts.css">

    <!-- Custom icons (Font Awesome replacement) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">
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
      <img src="/CeylonGO/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/transporter/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a>
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
        <li class="active"><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <div class="profile-box">
          <h1>Welcome, <?php echo htmlspecialchars($user_name); ?> !</h1>
          <h5>Overview of your transport operators</h2>
        </div>
      </div>

      <!-- Quick Stats Row -->
      <?php
        $confirmedCount = is_array($confirmed_bookings) ? count($confirmed_bookings) : 0;
        $pendingCount = is_array($pending_bookings) ? count($pending_bookings) : 0;
        $totalTrips = $confirmedCount;
      ?>
      <div class="quick-stats-row">
        <div class="stat-card trips">
          <div class="stat-icon">
            <i class="fa-solid fa-car-side"></i>
          </div>
          <div class="stat-content">
            <h3><?= $totalTrips ?></h3>
            <p>Confirmed Trips</p>
          </div>
        </div>

        <div class="stat-card customers">
          <div class="stat-icon">
            <i class="fa-regular fa-clock"></i>
          </div>
          <div class="stat-content">
            <h3><?= $pendingCount ?></h3>
            <p>Pending Bookings</p>
          </div>
        </div>

        <div class="stat-card distance">
          <div class="stat-icon">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <div class="stat-content">
            <h3><?= $confirmedCount + $pendingCount ?></h3>
            <p>Total Bookings</p>
          </div>
        </div>

        <div class="stat-card rating">
          <div class="stat-icon">
            <i class="fa-solid fa-star"></i>
          </div>
          <div class="stat-content">
            <h3>4.2</h3>
            <p>Average Rating</p>
          </div>
        </div>
      </div>

      <!-- Dashboard Widgets Container -->
      <div class="dashboard-widgets">
        <!-- Tour Calendar -->
        <div class="tour-calendar">
          <div class="calendar-top" id="selectedDate">
            Sunday, December 14
            <span class="icon icon-chevron-down"></span>
          </div>

          <div class="calendar-body">
            <div class="calendar-month">
              <button id="prevMonth" type="button" style="cursor:pointer;"><i class="fa-solid fa-chevron-left"></i></button>
              <span id="monthYear"></span>
              <button id="nextMonth" type="button" style="cursor:pointer;"><i class="fa-solid fa-chevron-right"></i></button>
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
          // Set time to midnight for accurate date comparison
          today.setHours(0, 0, 0, 0);
          bookingDate.setHours(0, 0, 0, 0);
          return bookingDate < today;
        }

        // Real booking data from the database
        const allBookingData = <?php
          $calendarData = [];
          if (is_array($confirmed_bookings)) {
            foreach ($confirmed_bookings as $b) {
              $calendarData[] = [
                'date' => $b['date'],
                'status' => 'upcoming',
                'customerName' => $b['customer_name'],
                'vehicleType' => $b['vehicle_type'],
                'pickupTime' => date('h:i A', strtotime($b['pickup_time'])),
                'pickupLocation' => $b['pickup_location'],
                'dropoffLocation' => $b['dropoff_location'],
                'numPeople' => $b['num_people'],
                'notes' => $b['notes'] ?? '',
                'bookingId' => $b['id']
              ];
            }
          }
          if (is_array($pending_bookings)) {
            foreach ($pending_bookings as $b) {
              $calendarData[] = [
                'date' => $b['date'],
                'status' => 'pending',
                'customerName' => $b['customer_name'],
                'vehicleType' => $b['vehicle_type'],
                'pickupTime' => date('h:i A', strtotime($b['pickup_time'])),
                'pickupLocation' => $b['pickup_location'],
                'dropoffLocation' => $b['dropoff_location'],
                'numPeople' => $b['num_people'],
                'notes' => $b['notes'] ?? '',
                'bookingId' => $b['id']
              ];
            }
          }
          echo json_encode($calendarData);
        ?>;

        // Filter out past dates - only show future bookings
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
              // Determine status
              const hasPending = bookingsOnDate.some(b => b.status === "pending");
              const hasUpcoming = bookingsOnDate.some(b => b.status === "upcoming");
              
              if (hasPending && hasUpcoming) {
                dateDiv.classList.add("booking-mixed");
              } else if (hasPending) {
                dateDiv.classList.add("booking-cancelled");
              } else {
                dateDiv.classList.add("booking-upcoming");
              }
              
              // Store booking data for click event
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
              selectedDateText.innerHTML =
                `${monthNames[month]} ${d}, ${year} <span class="icon icon-chevron-down"></span>`;
              
              // Show booking details if available
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
                <p><span class="icon icon-car"></span> <strong>Vehicle:</strong> ${booking.vehicleType}</p>
                <p><span class="icon icon-clock"></span> <strong>Pickup Time:</strong> ${booking.pickupTime}</p>
                <p><span class="icon icon-location"></span> <strong>From:</strong> ${booking.pickupLocation}</p>
                <p><span class="icon icon-location"></span> <strong>To:</strong> ${booking.dropoffLocation}</p>
                <p><span class="icon icon-users"></span> <strong>Passengers:</strong> ${booking.numPeople}</p>
                ${booking.notes ? `<p><span class="icon icon-note"></span> <strong>Notes:</strong> ${booking.notes}</p>` : ""}
              </div>
            `;
            bookingList.appendChild(bookingCard);
          });
          
          modal.style.display = "flex";
        }
        // Calendar already rendered above
        </script>

        <!-- Pending Booking Requests -->
        <div class="pending-requests-card">
          <div class="pending-requests-header">
            Pending Booking Requests
            <span class="icon icon-clock"></span>
          </div>

          <div class="pending-requests-body" id="pendingRequestsBody">
            <!-- Pending requests will be dynamically populated -->
          </div>
          
          <script>
            // Populate pending bookings list from real DB data
            function populatePendingBookings() {
              const pendingRequestsBody = document.getElementById('pendingRequestsBody');
              
              // Filter for only pending bookings and future dates
              const pendingList = bookingData.filter(booking => 
                booking.status === 'pending' && !isPastDate(booking.date)
              );
              
              if (pendingList.length === 0) {
                pendingRequestsBody.innerHTML = '<p style="text-align: center; padding: 20px; color: #666;">No pending bookings</p>';
                return;
              }
              
              pendingRequestsBody.innerHTML = '';
              
              pendingList.forEach((booking) => {
                const bookingDate = new Date(booking.date);
                const formattedDate = bookingDate.toLocaleDateString('en-US', { 
                  month: 'short', 
                  day: 'numeric', 
                  year: 'numeric' 
                });
                
                const requestCard = document.createElement('a');
                requestCard.href = `/CeylonGo/public/transporter/pending_info?id=${booking.bookingId}`;
                requestCard.className = 'pending-request-card';
                requestCard.innerHTML = `
                  <div class="request-header">
                    <span class="icon icon-user"></span>
                    <h4>${booking.customerName}</h4>
                  </div>
                  <div class="request-details">
                    <p><span class="icon icon-car"></span> ${booking.vehicleType}</p>
                    <p><span class="icon icon-calendar"></span> ${formattedDate}</p>
                    <p><span class="icon icon-location"></span> ${booking.pickupLocation} → ${booking.dropoffLocation}</p>
                  </div>
                  <span class="request-badge">New</span>
                `;
                
                pendingRequestsBody.appendChild(requestCard);
              });
            }
            
            // Call this after bookingData is filtered
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
    // Modal close functionality - must be after modal HTML
    document.addEventListener('DOMContentLoaded', function() {
      const closeBtn = document.getElementById("closeModal");
      const modal = document.getElementById("bookingModal");
      
      // Close modal when clicking the X button
      if (closeBtn) {
        closeBtn.onclick = function() {
          modal.style.display = "none";
        };
      }
      
      // Close modal when clicking outside of it
      window.onclick = function(event) {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      };
    });
  </script>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

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
      
      // Close sidebar when clicking a link (mobile)
      const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            closeSidebar();
          }
        });
      });
      
      // Close sidebar on window resize if switching to desktop
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeSidebar();
        }
      });
    });
  </script>

  <!-- Profile Dropdown Script -->
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profilePic = document.querySelector('.profile-pic');
      
      if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
        dropdown.classList.remove('show');
      }
    });
  </script>

</body>
</html>
