/**
 * Admin Reports — Chart.js: bookings/month, revenue, user growth.
 * Expects window.REPORT_CHARTS when a report has been generated (else null).
 */
(function () {
    'use strict';

    function chartOpts(title, isCurrency) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const v = ctx.parsed.y;
                            if (v === undefined || v === null) return '';
                            return isCurrency
                                ? ' LKR ' + Number(v).toLocaleString('en-US', { minimumFractionDigits: 2 })
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
                            ? function (v) { return 'LKR ' + Number(v).toLocaleString(); }
                            : undefined
                    }
                }
            }
        };
    }

    function buildChart(canvasId, labels, data, label, type, isCurrency) {
        const el = document.getElementById(canvasId);
        if (!el || !labels || !data) return null;
        const ctx = el.getContext('2d');
        const cfg = {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: type === 'line'
                        ? 'rgba(25, 135, 84, 0.12)'
                        : 'rgba(13, 110, 253, 0.65)',
                    borderColor: type === 'line' ? 'rgba(25, 135, 84, 1)' : 'rgba(13, 110, 253, 1)',
                    borderWidth: type === 'line' ? 2 : 1,
                    fill: type === 'line',
                    tension: 0.3
                }]
            },
            options: chartOpts(label, !!isCurrency)
        };
        if (type === 'bar') {
            cfg.data.datasets[0].borderRadius = 4;
        }
        return new Chart(ctx, cfg);
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!window.REPORT_CHARTS || typeof window.REPORT_CHARTS !== 'object') return;

        const c = window.REPORT_CHARTS;
        if (c.bookingsMonthly && c.bookingsMonthly.labels && c.bookingsMonthly.labels.length) {
            buildChart('chartBookingsMonthly', c.bookingsMonthly.labels, c.bookingsMonthly.values, 'Bookings', 'bar', false);
        }
        if (c.revenue && c.revenue.labels && c.revenue.labels.length) {
            buildChart('chartRevenueTrend', c.revenue.labels, c.revenue.values, 'Revenue', 'line', true);
        }
        if (c.userGrowth && c.userGrowth.labels && c.userGrowth.labels.length) {
            buildChart('chartUserGrowth', c.userGrowth.labels, c.userGrowth.values, 'New accounts', 'bar', false);
        }
    });
})();
