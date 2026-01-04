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

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">  
    
    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/responsive.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/CeylonGO/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="/CeylonGo/views/tourist/tourist_dashboard.php">Logout</a>
     <img src="/CeylonGO/public/images/profile.jpg" alt="User" class="profile-pic">
    </nav>
  </header>
  
  <div class="page-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li class="active"><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <div class="profile-box">
          <h1>Welcome, Kaveesha Dulanjani !</h1>
          <h5>Overview of your transport operators</h2>
        </div>
      </div>

      <!-- Summary Overview -->
       <div class="summary">
        <div class="summary-card">
          <h2>40</h2>
          <p>Total Bookings</p>
        </div>
        </div>
      <div class="summary">
        <div class="summary-card">
          <h2>12</h2>
          <p>Upcoming Bookings</p>
        </div>
        <div class="summary-card">
          <h2>5</h2>
          <p>Pending Bookings</p>
        </div>
        <div class="summary-card">
          <h2>3</h2>
          <p>Cancelled Bookings</p>
        </div>
        <div class="summary-card">
          <h2>20</h2>
          <p>Completed Bookings</p>
        </div>
      </div>

      <!-- Performance Chart and Calendar -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
        <div class="chart-container">
          <h3>Performance Overview</h3>
          <canvas id="performanceChart"></canvas>
        </div>
        <div class="calendar-container" style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <h3 style="margin-bottom: 15px; color: var(--color-primary); font-size: 1.1rem;">Upcoming Bookings</h3>
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    .calendar-container {
      font-family: inherit;
    }
    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px 0;
    }
    .calendar-nav-btn {
      background: var(--color-primary);
      color: white;
      border: none;
      padding: 5px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    .calendar-nav-btn:hover {
      background: var(--color-primary-600);
    }
    .calendar-month-year {
      font-weight: 600;
      color: var(--color-dark-text);
      font-size: 14px;
    }
    .calendar-weekdays {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 2px;
      margin-bottom: 5px;
    }
    .calendar-weekday {
      text-align: center;
      font-size: 11px;
      font-weight: 600;
      color: var(--color-light-text);
      padding: 5px 0;
    }
    .calendar-days {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 2px;
    }
    .calendar-day {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      border-radius: 4px;
      cursor: pointer;
      position: relative;
      min-height: 30px;
    }
    .calendar-day.other-month {
      color: #ccc;
    }
    .calendar-day.today {
      background: var(--color-primary);
      color: white;
      font-weight: 600;
    }
    .calendar-day.has-booking {
      background: #e8f5e8;
      color: var(--color-primary);
      font-weight: 600;
    }
    .calendar-day.has-booking::after {
      content: '';
      position: absolute;
      bottom: 2px;
      left: 50%;
      transform: translateX(-50%);
      width: 4px;
      height: 4px;
      background: var(--color-primary);
      border-radius: 50%;
    }
  </style>

  <script>
    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Successful', 'Cancelled'],
        datasets: [{
          label: 'Success Rate',
          data: [20, 6],
          backgroundColor: ['#4caf50', '#f44336'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    // Custom Calendar Implementation
    (function() {
      let currentDate = new Date();
      let bookings = [];
      
      function renderCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();
        
        // Get previous month's last days for display
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        let html = `
          <div class="calendar-header">
            <button class="calendar-nav-btn" onclick="transportCalendar.changeMonth(-1)">‹</button>
            <div class="calendar-month-year">${monthNames[month]} ${year}</div>
            <button class="calendar-nav-btn" onclick="transportCalendar.changeMonth(1)">›</button>
          </div>
          <div class="calendar-weekdays">
            ${dayNames.map(day => `<div class="calendar-weekday">${day}</div>`).join('')}
          </div>
          <div class="calendar-days">
        `;
        
        // Previous month's days (for visual completeness)
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
          const day = prevMonthLastDay - i;
          html += `<div class="calendar-day other-month">${day}</div>`;
        }
        
        // Current month's days
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
          const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
          const isToday = year === today.getFullYear() && month === today.getMonth() && day === today.getDate();
          
          // Check if this date has bookings
          const hasBooking = bookings.some(b => {
            if (!b.start) return false;
            const startDate = b.start.split('T')[0];
            return startDate === dateStr;
          });
          
          let classes = 'calendar-day';
          if (isToday) classes += ' today';
          if (hasBooking) classes += ' has-booking';
          
          html += `<div class="${classes}" onclick="transportCalendar.showBookings('${dateStr}')">${day}</div>`;
        }
        
        // Next month's days to fill the grid (up to 42 cells total)
        const totalCells = startingDayOfWeek + daysInMonth;
        const remainingCells = 42 - totalCells;
        for (let day = 1; day <= remainingCells && day <= 14; day++) {
          html += `<div class="calendar-day other-month">${day}</div>`;
        }
        
        html += '</div>';
        calendarEl.innerHTML = html;
      }
      
      // Global calendar object
      window.transportCalendar = {
        changeMonth: function(direction) {
          currentDate.setMonth(currentDate.getMonth() + direction);
          renderCalendar();
        },
        showBookings: function(dateStr) {
          const dayBookings = bookings.filter(b => {
            if (!b.start) return false;
            const startDate = b.start.split('T')[0];
            return startDate === dateStr;
          });
          if (dayBookings.length > 0) {
            let message = `Bookings on ${dateStr}:\n\n`;
            dayBookings.forEach(b => {
              message += `Booking #${b.id}\n`;
              if (b.customerName) message += `Customer: ${b.customerName}\n`;
              if (b.location) message += `Location: ${b.location}\n`;
              if (b.time) message += `Time: ${b.time}\n`;
              message += '\n';
            });
            alert(message);
          } else {
            alert(`No bookings on ${dateStr}`);
          }
        },
        setBookings: function(data) {
          bookings = data || [];
          renderCalendar();
        }
      };
      
      // Fetch bookings on load
      document.addEventListener('DOMContentLoaded', function() {
        fetch('/CeylonGo/public/transporter/bookings-calendar')
          .then(response => response.json())
          .then(data => {
            window.transportCalendar.setBookings(data);
          })
          .catch(error => {
            console.error('Error fetching bookings:', error);
            renderCalendar();
          });
      });
      
      // Initial render
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderCalendar);
      } else {
        renderCalendar();
      }
    })();
  </script>
</body>
</html>
