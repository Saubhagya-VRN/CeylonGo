document.addEventListener("DOMContentLoaded", function() {

    function renderBarChart(canvasId, labelText, color) {
        const canvas = document.getElementById(canvasId);
        
        if (!canvas) {
            console.error(`Canvas with ID "${canvasId}" not found`);
            return null;
        }

        // Read data from canvas attributes
        const labelsAttr = canvas.dataset.labels;
        const valuesAttr = canvas.dataset.values;

        console.log(`${canvasId} - Labels:`, labelsAttr);
        console.log(`${canvasId} - Values:`, valuesAttr);

        let labels, values;

        try {
            labels = JSON.parse(labelsAttr || '[]');
            values = JSON.parse(valuesAttr || '[]');
        } catch (e) {
            console.error(`Error parsing data for ${canvasId}:`, e);
            labels = [];
            values = [];
        }

        // Convert string values to numbers
        values = values.map(v => parseInt(v) || 0);

        console.log(`${canvasId} - Parsed Labels:`, labels);
        console.log(`${canvasId} - Parsed Values:`, values);

        return new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: labelText,
                    data: values,
                    backgroundColor: color,
                    borderColor: color,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    }

    // Render both charts
    const bookingsChart = renderBarChart('bookingsChart', 'Bookings', '#2c3e50');
    const cancellationsChart = renderBarChart('cancellationsChart', 'Cancellations', '#e74c3c');

    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const period = this.getAttribute('data-period') || this.textContent.toLowerCase();
            const url = new URL(window.location.href);
            url.searchParams.set('period', period);
            window.location.href = url.toString();
        });
    });

    // Export buttons
    document.querySelectorAll('.footer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.textContent.toLowerCase();
            if (action.includes('pdf')) exportChartsPDF();
            if (action.includes('excel')) exportChartsExcel();
        });
    });

    // PDF Export using html2canvas + jsPDF
    function exportChartsPDF() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();
        
        const bookingsCanvas = document.getElementById('bookingsChart');
        const cancellationsCanvas = document.getElementById('cancellationsChart');
        
        let chartsProcessed = 0;

        html2canvas(bookingsCanvas).then(canvasImg => {
            const imgData = canvasImg.toDataURL('image/png');
            pdf.text('Number of Bookings', 10, 10);
            pdf.addImage(imgData, 'PNG', 10, 20, 180, 90);
            chartsProcessed++;

            if (chartsProcessed === 2) {
                pdf.save('report.pdf');
            }
        });

        html2canvas(cancellationsCanvas).then(canvasImg => {
            const imgData = canvasImg.toDataURL('image/png');
            pdf.addPage();
            pdf.text('Cancellations', 10, 10);
            pdf.addImage(imgData, 'PNG', 10, 20, 180, 90);
            chartsProcessed++;

            if (chartsProcessed === 2) {
                pdf.save('report.pdf');
            }
        });
    }

    // Excel export (CSV)
    function exportChartsExcel() {
        const bookingsCanvas = document.getElementById('bookingsChart');
        const cancellationsCanvas = document.getElementById('cancellationsChart');

        const labels = JSON.parse(bookingsCanvas.dataset.labels || '[]');
        const bookings = JSON.parse(bookingsCanvas.dataset.values || '[]');
        const cancellations = JSON.parse(cancellationsCanvas.dataset.values || '[]');

        const table = [['Period', 'Bookings', 'Cancellations']];

        labels.forEach((label, i) => {
            table.push([
                label, 
                bookings[i] || 0, 
                cancellations[i] || 0
            ]);
        });

        let csvContent = "data:text/csv;charset=utf-8,"
            + table.map(e => e.join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "report.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

});