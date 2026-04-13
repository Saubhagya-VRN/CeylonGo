/**
 * Reports & Analysis — one Chart.js visualization matching current report filters.
 * Expects window.REPORT_PAGE_CHART when a report has been generated with chart data.
 */
(function () {
    'use strict';

    function chartOpts(isCurrency) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const v = ctx.parsed.y;
                            if (v === undefined || v === null) {
                                return '';
                            }
                            return isCurrency
                                ? ' LKR ' + Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
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
                            ? function (v) {
                                return 'LKR ' + Number(v).toLocaleString();
                            }
                            : undefined
                    }
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var cfg = window.REPORT_PAGE_CHART;
        if (!cfg || typeof cfg !== 'object' || !cfg.labels || !cfg.values) {
            return;
        }
        if (!cfg.labels.length) {
            return;
        }

        var canvas = document.getElementById('reportChartCanvas');
        if (!canvas) {
            return;
        }

        var isCurrency = cfg.valueKind === 'currency';
        var type = cfg.chartKind === 'line' ? 'line' : 'bar';
        var label = cfg.title || 'Series';

        var ctx = canvas.getContext('2d');
        var dataset = {
            label: label,
            data: cfg.values,
            backgroundColor: type === 'line'
                ? 'rgba(25, 135, 84, 0.12)'
                : 'rgba(13, 110, 253, 0.65)',
            borderColor: type === 'line' ? 'rgba(25, 135, 84, 1)' : 'rgba(13, 110, 253, 1)',
            borderWidth: type === 'line' ? 2 : 1,
            fill: type === 'line',
            tension: 0.3
        };
        if (type === 'bar') {
            dataset.borderRadius = 4;
        }

        new Chart(ctx, {
            type: type,
            data: {
                labels: cfg.labels,
                datasets: [dataset]
            },
            options: chartOpts(isCurrency)
        });
    });
})();
