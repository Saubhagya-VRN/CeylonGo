/**
 * reports_charts.js
 * Handles:
 *  - Rendering Bookings, Revenue, and Cancellations charts (Chart.js)
 *  - Period filter buttons (daily / weekly / monthly / yearly)
 *  - Booking-type filter buttons (both / package / custom)
 *  - Download as PDF  (html2canvas + jsPDF)
 *  - Download as Excel (SheetJS / xlsx)
 */

(function () {
    'use strict';

    /* ── Chart instances ──────────────────────────────────────────────────── */
    let bookingsChart     = null;
    let revenueChart      = null;
    let cancellationsChart = null;

    /* ── Build / rebuild all three charts ────────────────────────────────── */
    function buildCharts(data) {
        const { labels, bookings, revenue, cancellations } = data;

        if (bookingsChart)      bookingsChart.destroy();
        if (revenueChart)       revenueChart.destroy();
        if (cancellationsChart) cancellationsChart.destroy();

        bookingsChart = new Chart(
            document.getElementById('bookingsChart').getContext('2d'),
            {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Bookings',
                        data: bookings,
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor:     'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: chartOptions('Number of Bookings', false)
            }
        );

        revenueChart = new Chart(
            document.getElementById('revenueChart').getContext('2d'),
            {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue (LKR)',
                        data: revenue,
                        borderColor:     'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.12)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(25, 135, 84, 1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: chartOptions('Revenue (LKR)', true)
            }
        );

        cancellationsChart = new Chart(
            document.getElementById('cancellationsChart').getContext('2d'),
            {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Cancellations / Refunds',
                        data: cancellations,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor:     'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: chartOptions('Cancellations', false)
            }
        );
    }

    /* ── Shared chart options ─────────────────────────────────────────────── */
    function chartOptions(title, isCurrency) {
        return {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const v = ctx.parsed.y;
                            return isCurrency
                                ? ' LKR ' + Number(v).toLocaleString('en-US', {minimumFractionDigits: 2})
                                : ' ' + v;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: isCurrency
                            ? v => 'LKR ' + Number(v).toLocaleString()
                            : v => v
                    }
                }
            }
        };
    }

    /* ── Update stat boxes ────────────────────────────────────────────────── */
    function updateStats(totalBookings, totalRevenue, totalCancellations) {
        document.getElementById('statTotalBookings').textContent     = totalBookings;
        document.getElementById('statTotalRevenue').textContent      =
            'LKR ' + Number(totalRevenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('statTotalCancellations').textContent = totalCancellations;
    }

    /* ── Fetch new data from server and re-render ─────────────────────────── */
    function fetchAndRender(period, bookingType) {
        const url = `/CeylonGo/public/admin/reports?period=${period}&booking_type=${bookingType}&ajax=1`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(json => {
                buildCharts(json);
                updateStats(json.totalBookings, json.totalRevenue, json.totalCancellations);
                // Update browser URL without reload
                history.replaceState(null, '', `/CeylonGo/public/admin/reports?period=${period}&booking_type=${bookingType}`);
            })
            .catch(err => console.error('Reports fetch error:', err));
    }

    /* ── Period filter buttons ────────────────────────────────────────────── */
    function initPeriodFilter() {
        document.querySelectorAll('.filter-btn[data-period]').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn[data-period]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                fetchAndRender(this.dataset.period, activeBookingType());
            });
        });
    }

    /* ── Booking-type filter buttons ──────────────────────────────────────── */
    function initTypeFilter() {
        document.querySelectorAll('.type-btn[data-type]').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.type-btn[data-type]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                fetchAndRender(activePeriod(), this.dataset.type);
            });
        });
    }

    function activePeriod() {
        const btn = document.querySelector('.filter-btn[data-period].active');
        return btn ? btn.dataset.period : 'monthly';
    }

    function activeBookingType() {
        const btn = document.querySelector('.type-btn[data-type].active');
        return btn ? btn.dataset.type : 'both';
    }

    /* ── PDF Download ─────────────────────────────────────────────────────── */
    window.downloadChartsAsPDF = async function (selected) {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
        const pageW = pdf.internal.pageSize.getWidth();
        const margin = 14;
        let y = 16;

        pdf.setFontSize(16);
        pdf.setFont('helvetica', 'bold');
        pdf.text('Ceylon Go — Reports & Analysis', margin, y);
        y += 8;

        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(120);
        const period      = activePeriod();
        const bookingType = activeBookingType();
        const typeLabel   = bookingType === 'both' ? 'All Bookings'
                          : bookingType === 'package' ? 'Package Bookings' : 'Custom Trips';
        pdf.text(`Period: ${period}   |   Booking Type: ${typeLabel}   |   Generated: ${new Date().toLocaleDateString()}`, margin, y);
        pdf.setTextColor(0);
        y += 10;

        const sectionMap = {
            bookings:      'section-bookings',
            revenue:       'section-revenue',
            cancellations: 'section-cancellations',
        };

        for (const key of selected) {
            const el = document.getElementById(sectionMap[key]);
            if (!el) continue;

            const canvas = await html2canvas(el, { scale: 2, backgroundColor: '#ffffff' });
            const imgData = canvas.toDataURL('image/png');
            const imgW    = pageW - margin * 2;
            const imgH    = (canvas.height / canvas.width) * imgW;

            if (y + imgH > pdf.internal.pageSize.getHeight() - 10) {
                pdf.addPage();
                y = 14;
            }

            pdf.addImage(imgData, 'PNG', margin, y, imgW, imgH);
            y += imgH + 8;
        }

        pdf.save(`ceylongo_reports_${period}_${bookingType}.pdf`);
    };

    /* ── Excel Download ───────────────────────────────────────────────────── */
    window.downloadChartsAsExcel = function (selected) {
        const wb = XLSX.utils.book_new();
        const period      = activePeriod();
        const bookingType = activeBookingType();

        const labelEl = document.getElementById('bookingsChart');
        const labels  = JSON.parse(labelEl.getAttribute('data-labels') || '[]');

        if (selected.includes('bookings')) {
            const bookingsData = JSON.parse(labelEl.getAttribute('data-values') || '[]');
            const rows = [['Period', 'Bookings']];
            labels.forEach((l, i) => rows.push([l, bookingsData[i] || 0]));
            const ws = XLSX.utils.aoa_to_sheet(rows);
            XLSX.utils.book_append_sheet(wb, ws, 'Bookings');
        }

        if (selected.includes('revenue')) {
            const revEl  = document.getElementById('revenueChart');
            const revData = JSON.parse(revEl.getAttribute('data-values') || '[]');
            const rows = [['Period', 'Revenue (LKR)']];
            labels.forEach((l, i) => rows.push([l, revData[i] || 0]));
            const ws = XLSX.utils.aoa_to_sheet(rows);
            XLSX.utils.book_append_sheet(wb, ws, 'Revenue');
        }

        if (selected.includes('cancellations')) {
            const canEl   = document.getElementById('cancellationsChart');
            const canData = JSON.parse(canEl.getAttribute('data-values') || '[]');
            const rows = [['Period', 'Cancellations']];
            labels.forEach((l, i) => rows.push([l, canData[i] || 0]));
            const ws = XLSX.utils.aoa_to_sheet(rows);
            XLSX.utils.book_append_sheet(wb, ws, 'Cancellations');
        }

        XLSX.writeFile(wb, `ceylongo_reports_${period}_${bookingType}.xlsx`);
    };

    /* ── AJAX endpoint support ────────────────────────────────────────────── */
    // If AdminController detects ajax=1, it should return JSON instead of a full view.
    // The data-* attributes on the canvas elements are the initial server-rendered values.
    // After a filter change, we update charts directly from the JSON response.
    // The canvas data-* attributes are refreshed below so Excel export stays in sync.

    const _origFetch = window.fetchAndRender;
    const _origBuild = buildCharts;

    function patchCanvasData(json) {
        const bookingsEl      = document.getElementById('bookingsChart');
        const revenueEl       = document.getElementById('revenueChart');
        const cancellationsEl = document.getElementById('cancellationsChart');

        bookingsEl.setAttribute('data-labels', JSON.stringify(json.labels));
        bookingsEl.setAttribute('data-values', JSON.stringify(json.bookings));
        revenueEl.setAttribute('data-labels',  JSON.stringify(json.labels));
        revenueEl.setAttribute('data-values',  JSON.stringify(json.revenue));
        cancellationsEl.setAttribute('data-labels', JSON.stringify(json.labels));
        cancellationsEl.setAttribute('data-values', JSON.stringify(json.cancellations));
    }

    // Override fetchAndRender to also patch canvas data-* attributes
    window.fetchAndRender = function (period, bookingType) {
        const url = `/CeylonGo/public/admin/reports?period=${period}&booking_type=${bookingType}&ajax=1`;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(json => {
                buildCharts(json);
                patchCanvasData(json);
                updateStats(json.totalBookings, json.totalRevenue, json.totalCancellations);
                history.replaceState(null, '', `/CeylonGo/public/admin/reports?period=${period}&booking_type=${bookingType}`);
            })
            .catch(err => console.error('Reports fetch error:', err));
    };

    /* ── Init ─────────────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        // Initial render from server-injected data
        buildCharts(REPORT_DATA);
        updateStats(REPORT_DATA.totalBookings, REPORT_DATA.totalRevenue, REPORT_DATA.totalCancellations);

        initPeriodFilter();
        initTypeFilter();
    });

})();