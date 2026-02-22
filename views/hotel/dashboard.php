<?php
  // Optional: include session/auth and shared header/footer if available
  // @example
  // require_once __DIR__ . '/../includes/session.php';
  // include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go • Hotel Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/hotel/style.css" />
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
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
</head>
<body>
  <header class="navbar">
    <div class="branding">
      <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/hotel/dashboard">Home</a>
      <a href="/CeylonGo/public/logout" class="btn-login">Logout</a>
    </nav>
  </header>

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-text">Ceylon Go</div>
    </div>
    <nav class="nav">
      <a class="nav-link active" href="/CeylonGo/public/hotel/dashboard">Dashboard</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/availability">Availability</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/bookings">Bookings</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/add-room">Booking Management</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/payments">Payments</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/reviews">Reviews</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/inquiries">Inquiries</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/report-issue">Report Issue</a>
      <a class="nav-link" href="/CeylonGo/public/hotel/notifications">Notifications</a>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="left">
        <h1 class="page-title">Dashboard</h1>
        <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
      </div>
      <div class="right">
        <div class="datetime" id="currentDateTime">--</div>
      </div>
    </header>

    <section class="content">
      <div class="cards-grid">
        <div class="card stat-card" id="statTotalBookings">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value" data-key="totalBookings">20</div>
        </div>
        <div class="card stat-card" id="statPendingRequests">
          <div class="stat-label">Pending Requests</div>
          <div class="stat-value" data-key="pendingRequests">6</div>
        </div>
        <div class="card stat-card" id="statTotalReviews">
          <div class="stat-label">Total Reviews</div>
          <div class="stat-value" data-key="totalReviews">16</div>
        </div>
        <div class="card stat-card" id="statTotalEarnings">
          <div class="stat-label">Total Earnings</div>
          <div class="stat-value" data-key="totalEarnings">256987.67</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-header">
            <h2>Recent Bookings</h2>
          </div>
          <div class="panel-body table-wrap">
            <table class="table" id="bookingsTable">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Guest</th>
                  <th>Check-in</th>
                  <th>Check-out</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <!-- Rows injected by JS -->
              </tbody>
            </table>
          </div>
        </div>

        <div class="panel">
          <div class="panel-header">
            <h2>Notifications</h2>
          </div>
          <div class="panel-body notifications" id="notificationsList">
            <!-- Notifications injected by JS -->
          </div>
        </div>
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-header">
            <h2>Monthly Revenue</h2>
          </div>
          <div class="panel-body chart-wrap">
            <canvas id="revenueChart" height="110"></canvas>
          </div>
        </div>
        <div class="panel">
          <div class="panel-header">
            <h2>Upcoming Bookings</h2>
          </div>
          <div class="panel-body">
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="../../public/js/hotel.js"></script>
  <script>
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
            <button class="calendar-nav-btn" onclick="hotelCalendar.changeMonth(-1)">‹</button>
            <div class="calendar-month-year">${monthNames[month]} ${year}</div>
            <button class="calendar-nav-btn" onclick="hotelCalendar.changeMonth(1)">›</button>
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
          
          // Check if this date has bookings (check both start and end dates)
          const hasBooking = bookings.some(b => {
            if (!b.start) return false;
            const startDate = b.start.split('T')[0];
            const endDate = b.end ? b.end.split('T')[0] : startDate;
            return (dateStr >= startDate && dateStr <= endDate);
          });
          
          let classes = 'calendar-day';
          if (isToday) classes += ' today';
          if (hasBooking) classes += ' has-booking';
          
          html += `<div class="${classes}" onclick="hotelCalendar.showBookings('${dateStr}')">${day}</div>`;
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
      window.hotelCalendar = {
        changeMonth: function(direction) {
          currentDate.setMonth(currentDate.getMonth() + direction);
          renderCalendar();
        },
        showBookings: function(dateStr) {
          const dayBookings = bookings.filter(b => {
            if (!b.start) return false;
            const startDate = b.start.split('T')[0];
            const endDate = b.end ? b.end.split('T')[0] : startDate;
            return (dateStr >= startDate && dateStr <= endDate);
          });
          if (dayBookings.length > 0) {
            let message = `Bookings on ${dateStr}:\n\n`;
            dayBookings.forEach(b => {
              message += `Booking #${b.id}\n`;
              if (b.guest) message += `Guest: ${b.guest}\n`;
              if (b.room) message += `Room: ${b.room}\n`;
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
        fetch('/CeylonGo/public/hotel/bookings-calendar')
          .then(response => response.json())
          .then(data => {
            window.hotelCalendar.setBookings(data);
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
<?php
  // Optional: include shared footer if available
  // include __DIR__ . '/../includes/footer.php';
?>
</body>
</html>


