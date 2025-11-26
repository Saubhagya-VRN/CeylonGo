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

    // Update datetime every minute
    setInterval(updateDateTime, 60000);

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
});
