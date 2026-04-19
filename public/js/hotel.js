// Hotel Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Update current date and time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        const dateTimeString = now.toLocaleDateString('en-US', options);
        
        const dateTimeElements = document.querySelectorAll('#currentDateTime');
        dateTimeElements.forEach(element => {
            element.textContent = dateTimeString;
        });
    }

    // Update hotel name (placeholder functionality)
    function updateHotelName() {
        const hotelNameElements = document.querySelectorAll('#hotelName');
        hotelNameElements.forEach(element => {
            // This would typically come from a database or session
            // For now, using a placeholder
            if (element.textContent === 'Hotel') {
                element.textContent = 'Sample Hotel';
            }
        });
    }

    // Initialize
    updateDateTime();
    updateHotelName();
    fetchDashboardStats();
    fetchRecentBookings();
    fetchRevenueData();
    fetchAvailabilityData();

    // Update datetime every minute
    setInterval(updateDateTime, 60000);

    // Fetch and update availability table (Next 14 Days)
    function fetchAvailabilityData() {
        const tableBody = document.querySelector('#availabilityTable tbody');
        if (!tableBody) return;

        console.log('Fetching availability data...');
        fetch('/CeylonGo/public/hotel/availability-data')
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center">No availability data found</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.date}</td>
                        <td class="${item.Single > 0 ? '' : 'text-danger'}">${item.Single > 0 ? item.Single + ' Avail' : 'Full'}</td>
                        <td class="${item.Double > 0 ? '' : 'text-danger'}">${item.Double > 0 ? item.Double + ' Avail' : 'Full'}</td>
                        <td class="${item.Suite > 0 ? '' : 'text-danger'}">${item.Suite > 0 ? item.Suite + ' Avail' : 'Full'}</td>
                        <td class="${item.Deluxe > 0 ? '' : 'text-danger'}">${item.Deluxe > 0 ? item.Deluxe + ' Avail' : 'Full'}</td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching availability:', error);
                tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center">Error loading availability</td></tr>';
            });
    }

    // Fetch and update dashboard summary stats
    function fetchDashboardStats() {
        console.log('Fetching dashboard stats...');
        fetch('/CeylonGo/public/hotel/dashboard-stats')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Stats received:', data);
                updateStatValue('totalBookings', data.totalBookings);
                updateStatValue('pendingRequests', data.pendingRequests);
                updateStatValue('totalReviews', data.totalReviews);
                updateStatValue('totalEarnings', parseFloat(data.totalEarnings).toLocaleString());
                
                // Update Hotel Name
                const nameEl = document.getElementById('hotelName');
                if (nameEl && data.hotelName) {
                    nameEl.textContent = data.hotelName;
                }
            })
            .catch(error => console.error('Error fetching stats:', error));
    }

    function updateStatValue(key, value) {
        const el = document.querySelector(`.stat-value[data-key="${key}"]`);
        if (el) el.textContent = value;
    }

    // Fetch and update monthly revenue chart
    function fetchRevenueData() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        fetch('/CeylonGo/public/hotel/revenue-data')
            .then(response => response.json())
            .then(data => {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Monthly Revenue (LKR)',
                            data: data.data,
                            borderColor: '#2db44d',
                            backgroundColor: 'rgba(45, 180, 77, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching revenue data:', error));
    }

    // Fetch and update recent bookings table
    function fetchRecentBookings() {
        const tableBody = document.querySelector('#bookingsTable tbody');
        if (!tableBody) return;

        fetch('/CeylonGo/public/hotel/recent-bookings')
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center">No recent bookings found</td></tr>';
                    return;
                }

                data.forEach(booking => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#BK-${booking.id}</td>
                        <td>${booking.guest_name}</td>
                        <td>${booking.check_in}</td>
                        <td>${booking.check_out}</td>
                        <td>Rs. ${parseFloat(booking.total_price).toLocaleString()}</td>
                        <td><span class="status-badge ${booking.status.toLowerCase()}">${booking.status}</span></td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching recent bookings:', error);
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center">Error loading bookings</td></tr>';
            });
    }

    // Handle navigation link active states
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'dashboard.php')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Handle logout functionality
    const logoutLinks = document.querySelectorAll('.logout');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                // This would typically clear session and redirect
                window.location.href = 'login.php';
            }
        });
    });

    // Handle form submissions and other interactive elements
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Add loading states or validation as needed
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.textContent = 'Processing...';
                submitBtn.disabled = true;
            }
        });
    });

    // Room modal controls
    const roomModal = document.getElementById('addRoomModal');
    const openRoomModalButtons = document.querySelectorAll('[data-room-modal-open]');
    const closeRoomModalButtons = document.querySelectorAll('[data-room-modal-close]');

    function openRoomModal() {
        if (!roomModal) return;
        roomModal.classList.add('is-open');
        document.body.classList.add('room-modal-open');

        const firstField = roomModal.querySelector('input, textarea, select');
        if (firstField) {
            firstField.focus();
        }
    }

    function closeRoomModal() {
        if (!roomModal) return;
        roomModal.classList.remove('is-open');
        document.body.classList.remove('room-modal-open');
    }

    openRoomModalButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            openRoomModal();
        });
    });

    closeRoomModalButtons.forEach(button => {
        button.addEventListener('click', closeRoomModal);
    });

    if (roomModal) {
        roomModal.addEventListener('click', function(event) {
            if (event.target === roomModal) {
                closeRoomModal();
            }
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && roomModal && roomModal.classList.contains('is-open')) {
            closeRoomModal();
        }
    });

    // Dismiss success/error notices on room pages
    document.addEventListener('click', function(event) {
        const dismissButton = event.target.closest('[data-dismiss-notice]');
        if (!dismissButton) {
            return;
        }

        const notice = dismissButton.closest('.room-notice');
        if (notice) {
            notice.remove();
        }
    });
});
