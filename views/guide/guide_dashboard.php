<?php
// views/guide/guide_dashboard.php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Tour Guide Dashboard</title>
  <!-- Base styles -->
  <link rel="stylesheet" href="../../public/css/guide/base.css">
  <link rel="stylesheet" href="../../public/css/guide/navbar.css">
  <link rel="stylesheet" href="../../public/css/guide/sidebar.css">
  <link rel="stylesheet" href="../../public/css/guide/footer.css">
  
  <!-- Component styles -->
  <link rel="stylesheet" href="../../public/css/guide/cards.css">
  <link rel="stylesheet" href="../../public/css/guide/buttons.css">
  <link rel="stylesheet" href="../../public/css/guide/forms.css">
  
  <!-- Page-specific styles -->
  <link rel="stylesheet" href="../../public/css/guide/tables.css">
  <link rel="stylesheet" href="../../public/css/guide/profile.css">
  <link rel="stylesheet" href="../../public/css/guide/reviews.css">
  <link rel="stylesheet" href="../../public/css/guide/charts.css">

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="../../public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="guide_dashboard.php">Home</a>
      <a href="../tourist/tourist_dashboard.php">Logout</a>
      <img src="../../public/images/user.png" alt="User" class="profile-pic">
    </nav>
  </header>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li><a href="guide_dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="upcoming.php"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="pending.php"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="cancelled.php"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="review.php"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="profile.php"><i class="fa-regular fa-user"></i>Profile Management</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <div class="profile-box">
          <h1>Welcome, Priya Silva !</h1>
          <h5>Overview of your tour guide services</h2>
        </div>
      </div>

      <!-- Summary Overview -->
       <div class="summary">
        <div class="summary-card">
          <h2>25</h2>
          <p>Total Tours</p>
        </div>
        </div>
      <div class="summary">
        <div class="summary-card">
          <h2>8</h2>
          <p>Upcoming Tours</p>
        </div>
        <div class="summary-card">
          <h2>3</h2>
          <p>Pending Requests</p>
        </div>
        <div class="summary-card">
          <h2>2</h2>
          <p>Cancelled Tours</p>
        </div>
        <div class="summary-card">
          <h2>12</h2>
          <p>Completed Tours</p>
        </div>
      </div>

      <!-- Performance Chart and Calendar -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
        <div class="chart-container">
          <h3>Performance Overview</h3>
          <canvas id="performanceChart"></canvas>
        </div>
        <div class="calendar-container" style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <h3 style="margin-bottom: 15px; color: var(--color-primary); font-size: 1.1rem;">Upcoming Tours</h3>
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
          data: [12, 2],
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
        
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        let html = `
          <div class="calendar-header">
            <button class="calendar-nav-btn" onclick="guideCalendar.changeMonth(-1)">‹</button>
            <div class="calendar-month-year">${monthNames[month]} ${year}</div>
            <button class="calendar-nav-btn" onclick="guideCalendar.changeMonth(1)">›</button>
          </div>
          <div class="calendar-weekdays">
            ${dayNames.map(day => `<div class="calendar-weekday">${day}</div>`).join('')}
          </div>
          <div class="calendar-days">
        `;
        
        // Previous month's days
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
          
          html += `<div class="${classes}" onclick="guideCalendar.showBookings('${dateStr}')">${day}</div>`;
        }
        
        // Next month's days
        const totalCells = startingDayOfWeek + daysInMonth;
        const remainingCells = 42 - totalCells;
        for (let day = 1; day <= remainingCells && day <= 14; day++) {
          html += `<div class="calendar-day other-month">${day}</div>`;
        }
        
        html += '</div>';
        calendarEl.innerHTML = html;
      }
      
      // Global calendar object
      window.guideCalendar = {
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
            let message = `Tours on ${dateStr}:\n\n`;
            dayBookings.forEach(b => {
              message += `Tour #${b.id}\n`;
              if (b.touristName) message += `Tourist: ${b.touristName}\n`;
              if (b.location) message += `Location: ${b.location}\n`;
              if (b.time) message += `Time: ${b.time}\n`;
              if (b.status) message += `Status: ${b.status}\n`;
              message += '\n';
            });
            alert(message);
          } else {
            alert(`No tours on ${dateStr}`);
          }
        },
        setBookings: function(data) {
          bookings = data || [];
          renderCalendar();
        }
      };
      
      // Fetch bookings on load
      document.addEventListener('DOMContentLoaded', function() {
        fetch('/CeylonGo/public/guide/bookings-calendar')
          .then(response => response.json())
          .then(data => {
            window.guideCalendar.setBookings(data);
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
