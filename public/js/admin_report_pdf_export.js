/**
 * Build the same PDF export URL as Reports & Analysis (filters + summary + detail).
 * @param {string} type users|bookings|payments|providers
 * @param {{start: ?string, end: ?string}|null} range from resolveExportRange helpers (omit dates for all time)
 */
(function (global) {
    'use strict';

    var BASE = '/CeylonGo/public/admin/reports/export-pdf';

    function buildPdfUrl(type, range) {
        var parts = ['generated=1', 'type=' + encodeURIComponent(type)];
        if (range && range.start) {
            parts.push('date_from=' + encodeURIComponent(range.start));
        }
        if (range && range.end) {
            parts.push('date_to=' + encodeURIComponent(range.end));
        }
        return BASE + '?' + parts.join('&');
    }

    global.CeylonGoReportPdf = {
        buildPdfUrl: buildPdfUrl
    };
})(window);
