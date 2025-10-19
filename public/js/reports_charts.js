// Bookings Over Time (Bar Chart)
new Chart(document.getElementById("bookingsChart"), {
    type: 'bar',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [{
            label: "Bookings",
            data: [200, 170, 140, 180, 160, 175],
            backgroundColor: "#2c3e50"
        }]
    }
});

// Revenue Trends (Line Chart)
new Chart(document.getElementById("revenueChart"), {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [{
            label: "Revenue",
            data: [5000, 4200, 3000, 4500, 4800, 5300],
            fill: true,
            borderColor: "#1abc9c",
            backgroundColor: "rgba(26,188,156,0.2)"
        }]
    }
});

// Cancellations Distribution (Pie Chart)
new Chart(document.getElementById("cancellationsChart"), {
    type: 'pie',
    data: {
        labels: ["Late Cancel", "No-show", "Other"],
        datasets: [{
            data: [40, 35, 25],
            backgroundColor: ["#e74c3c", "#f1c40f", "#95a5a6"]
        }]
    }
});
