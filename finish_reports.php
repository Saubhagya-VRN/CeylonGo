<?php
// Fix for transport/report.php
$transportFile = "c:\\xampp\\htdocs\\CeylonGo\\views\\transport\\report.php";
if (file_exists($transportFile)) {
    $content = file_get_contents($transportFile);
    
    // 1. Ensure generatedAt is there
    if (!strpos($content, '$generatedAt =')) {
        $content = str_replace('// Period label', '$generatedAt = date("F d, Y \a\t h:i A");' . "\n" . '// Period label', $content);
    }
    
    // 2. Table Update: Replace the whole table block with the optimized one
    $tablePattern = '/<table class="report-table" id="tourTable">.*?<\/table>/s';
    $newTable = '<table class="report-table" id="tourTable">
                        <thead>
                            <tr>
                                <th>Tour ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Vehicle</th>
                                <th>Pax</th>
                                <th>Distance</th>
                                <th>Fare</th>
                            </tr>
                        </thead>
                        <tbody id="tourTableBody">
                            <?php if (empty($tours)): ?>
                                <tr class="no-data-row">
                                    <td colspan="8">
                                        <i class="fa-regular fa-folder-open"></i>
                                        No tour records found for the selected period.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr>
                                        <td class="tour-id">#TR<?= str_pad($tour["id"], 3, "0", STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($tour["customerName"]) ?></td>
                                        <td><?= date("M d, Y", strtotime($tour["date"])) ?></td>
                                        <td><?= htmlspecialchars($tour["pickup_location"] ?? "N/A") ?></td>
                                        <td><?= htmlspecialchars($tour["vehicle_type"]) ?></td>
                                        <td><?= htmlspecialchars($tour["pax"]) ?></td>
                                        <td><?= number_format($tour["distance"], 1) ?> km</td>
                                        <td class="fare-cell">Rs. <?= number_format($tour["fare"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($tours)): ?>
                        <tfoot style="background: rgba(0, 119, 182, 0.05); font-weight: 700;">
                            <tr>
                                <td colspan="5" style="text-align: right; padding: 14px 20px; color: #1a1a2e; font-size: 14px;">TOTALS</td>
                                <td style="border-top: 2px solid #0077b6; color: #0077b6; padding: 14px 16px;"><?= array_sum(array_column($tours, "pax")) ?></td>
                                <td style="border-top: 2px solid #0077b6; color: #0077b6; padding: 14px 16px;"><?= number_format(array_sum(array_column($tours, "distance")), 1) ?> km</td>
                                <td style="border-top: 2px solid #0077b6; color: #1a1a2e; padding: 14px 16px;">Rs. <?= number_format(array_sum(array_column($tours, "fare")), 2) ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>';
    $content = preg_replace($tablePattern, $newTable, $content);
    
    // 3. PDF Function: Replace basically the whole function body or major parts
    $pdfPattern = '/function downloadPDF\(\) \{.*?doc\.save\(.*?\.pdf\'\);/s';
    $newPdfBody = 'function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF(\'l\', \'mm\', \'a4\');
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // ---- HEADER ----
        doc.setFillColor(44, 85, 48); // System Green
        doc.rect(0, 0, pageWidth, 32, \'F\');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(22);
        doc.setFont(\'helvetica\', \'bold\');
        doc.text(\'Ceylon Go\', 14, 12);
        doc.setFontSize(12);
        doc.setFont(\'helvetica\', \'normal\');
        doc.text(\'Transport Performance Report\', 14, 20);
        doc.setFontSize(10);
        doc.text(\'User: <?= addslashes($user_name) ?>  |  Type: Transport Provider\', 14, 27);
        doc.text(\'Generated: <?= addslashes($generatedAt) ?>  |  Period: <?= addslashes($periodLabel) ?>\', pageWidth - 14, 27, { align: \'right\' });

        // ---- KPI SUMMARY ----
        let y = 40;
        doc.setTextColor(30, 30, 30);
        doc.setFontSize(13);
        doc.setFont(\'helvetica\', \'bold\');
        doc.text(\'Summary Metrics\', 14, y);
        y += 8;

        const kpiData = [
            [\'Total Revenue\', \'Rs. <?= number_format($kpi["total_revenue"], 2) ?>\'],
            [\'Avg. Fare\', \'Rs. <?= number_format($kpi["avg_fare"], 2) ?>\'],
            [\'Total Bookings\', \'<?= number_format($kpi["total_bookings"]) ?>\'],
            [\'Total Distance\', \'<?= number_format($kpi["total_distance"], 1) ?> km\'],
            [\'Total Passengers\', \'<?= number_format($kpi["total_passengers"]) ?>\'],
            [\'Completion Rate\', \'<?= $kpi["completion_rate"] ?>%\']
        ];

        doc.autoTable({
            startY: y,
            head: [[\'Metric\', \'Value\']],
            body: kpiData,
            theme: \'grid\',
            headStyles: { fillColor: [44, 85, 48], textColor: 255, fontStyle: \'bold\', fontSize: 10 },
            bodyStyles: { fontSize: 10 },
            columnStyles: { 0: { fontStyle: \'bold\', cellWidth: 50 }, 1: { cellWidth: 50 } },
            margin: { left: 14 },
            tableWidth: 100
        });

        // ---- TOUR DETAILS TABLE ----
        y = doc.lastAutoTable.finalY + 12;
        doc.setFontSize(13);
        doc.setFont(\'helvetica\', \'bold\');
        doc.text(\'Detailed Trip Summary\', 14, y);
        y += 4;

        // Build data from PHP
        const tourRows = [
            <?php foreach ($tours as $tour): ?>
            [
                \'#TR<?= str_pad($tour["id"], 3, "0", STR_PAD_LEFT) ?>\',
                \'<?= addslashes($tour["customerName"]) ?>\',
                \'<?= date("M d, Y", strtotime($tour["date"])) ?>\',
                \'<?= addslashes($tour["pickup_location"] ?? "N/A") ?>\',
                \'<?= addslashes($tour["vehicle_type"]) ?>\',
                \'<?= $tour["pax"] ?>\',
                \'<?= number_format($tour["distance"], 1) ?> km\',
                \'Rs. <?= number_format($tour["fare"], 2) ?>\'
            ],
            <?php endforeach; ?>
        ];

        // Totals
        const totalPax = <?= array_sum(array_column($tours, "pax")) ?>;
        const totalDist = <?= array_sum(array_column($tours, "distance")) ?>;
        const totalFare = <?= array_sum(array_column($tours, "fare")) ?>;

        doc.autoTable({
            startY: y,
            head: [[\'Tour ID\', \'Customer\', \'Date\', \'Location\', \'Vehicle\', \'Pax\', \'Distance\', \'Fare (LKR)\']],
            body: tourRows,
            foot: [[\'\', \'\', \'\', \'\', \'TOTALS\', totalPax, totalDist.toFixed(1) + \' km\', \'Rs. \' + totalFare.toLocaleString(\'en-US\', {minimumFractionDigits: 2})]],
            theme: \'grid\',
            headStyles: { fillColor: [44, 85, 48], textColor: 255, fontStyle: \'bold\', fontSize: 9, cellPadding: 3 },
            bodyStyles: { fontSize: 8.5, cellPadding: 2.5 },
            footStyles: { fillColor: [240, 248, 240], textColor: [30, 30, 30], fontStyle: \'bold\', fontSize: 10, cellPadding: 3 },
            columnStyles: {
                0: { cellWidth: 20 },
                1: { cellWidth: 40 },
                7: { halign: \'right\', fontStyle: \'bold\' }
            },
            margin: { left: 14, right: 14 }
        });

        // ---- FOOTER ----
        const totalPages = doc.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(\'Ceylon Go Transport Performance Report\', 14, pageHeight - 6);
            doc.text(\'Page \' + i + \' of \' + totalPages, pageWidth - 14, pageHeight - 6, { align: \'right\' });
        }

        doc.save(\'transport_performance_report.pdf\');';
    $content = preg_replace($pdfPattern, $newPdfBody, $content);
    
    file_put_contents($transportFile, $content);
}

// Fix for guide/report.php
$guideFile = "c:\\xampp\\htdocs\\CeylonGo\\views\\guide\\report.php";
if (file_exists($guideFile)) {
    $content = file_get_contents($guideFile);
    
    // 1. generatedAt
    if (!strpos($content, '$generatedAt =')) {
        $content = str_replace('// Defaults', '$generatedAt = date("F d, Y \a\t h:i A");' . "\n" . '// Defaults', $content);
    }
    
    // 2. Table Update - Removing Status Column
    $tablePattern = '/<table class="report-table" id="tourTable">.*?<\/table>/s';
    $newTable = '<table class="report-table" id="tourTable">
                        <thead>
                            <tr>
                                <th>Tour ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Language</th>
                                <th>Fee (LKR)</th>
                            </tr>
                        </thead>
                        <tbody id="tourTableBody">
                            <?php if (empty($tours)): ?>
                                <tr class="no-data-row">
                                    <td colspan="7">
                                        <i class="fa-regular fa-folder-open"></i>
                                        No tour records found for the selected period.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr>
                                        <td class="tour-id">#GT<?= str_pad($tour["id"], 3, "0", STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($tour["customerName"]) ?></td>
                                        <td><?= date("M d, Y", strtotime($tour["date"])) ?></td>
                                        <td><?= date("h:i A", strtotime($tour["time"])) ?></td>
                                        <td><?= htmlspecialchars($tour["location"]) ?></td>
                                        <td><span class="lang-tag"><?= htmlspecialchars($tour["language"]) ?></span></td>
                                        <td class="fee-cell">Rs. <?= number_format($tour["fee"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($tours)): ?>
                        <tfoot style="background: rgba(44, 85, 48, 0.05); font-weight: 700;">
                            <tr>
                                <td colspan="6" style="text-align: right; padding: 14px 20px; color: #1a1a2e; font-size: 14px;">TOTAL REVENUE</td>
                                <td style="border-top: 2px solid #2c5530; color: #1a1a2e; padding: 14px 16px;">Rs. <?= number_format(array_sum(array_column($tours, "fee")), 2) ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>';
    $content = preg_replace($tablePattern, $newTable, $content);

    // 3. PDF function
    $pdfPattern = '/function downloadPDF\(\) \{.*?doc\.save\(.*?\.pdf\'\);/s';
    $newPdfBody = 'function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF(\'l\', \'mm\', \'a4\');
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // ---- HEADER ----
        doc.setFillColor(44, 85, 48); // System Green
        doc.rect(0, 0, pageWidth, 32, \'F\');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(22);
        doc.setFont(\'helvetica\', \'bold\');
        doc.text(\'Ceylon Go\', 14, 12);
        doc.setFontSize(12);
        doc.setFont(\'helvetica\', \'normal\');
        doc.text(\'Guide Performance Report\', 14, 20);
        doc.setFontSize(10);
        doc.text(\'User: <?= addslashes($user_name) ?>  |  Type: Tour Guide\', 14, 27);
        doc.text(\'Generated: <?= addslashes($generatedAt) ?>  |  Period: <?= addslashes($periodLabel) ?>\', pageWidth - 14, 27, { align: \'right\' });

        // ---- KPI SUMMARY ----
        let y = 40;
        doc.setTextColor(30, 30, 30);
        doc.setFontSize(13);
        doc.setFont(\'helvetica\', \'bold\');
        doc.text(\'Performance metrics\', 14, y);
        y += 8;

        const kpiData = [
            [\'Total Revenue\', \'Rs. <?= number_format($kpi["total_revenue"], 2) ?>\'],
            [\'Avg. Fee\', \'Rs. <?= number_format($kpi["avg_fee"], 2) ?>\'],
            [\'Total Bookings\', \'<?= number_format($kpi["total_bookings"]) ?>\'],
            [\'Unique Clients\', \'<?= number_format($kpi["unique_clients"]) ?>\'],
            [\'Completion Rate\', \'<?= $kpi["completion_rate"] ?>%\']
        ];

        doc.autoTable({
            startY: y,
            head: [[\'Metric\', \'Value\']],
            body: kpiData,
            theme: \'grid\',
            headStyles: { fillColor: [44, 85, 48], textColor: 255, fontStyle: \'bold\', fontSize: 10 },
            bodyStyles: { fontSize: 10 },
            columnStyles: { 0: { fontStyle: \'bold\', cellWidth: 50 }, 1: { cellWidth: 50 } },
            margin: { left: 14 },
            tableWidth: 100
        });

        // ---- TOUR DETAILS TABLE ----
        y = doc.lastAutoTable.finalY + 12;
        doc.setFontSize(13);
        doc.setFont(\'helvetica\', \'bold\');
        doc.text(\'Detailed Tour History\', 14, y);
        y += 4;

        // Build table data from PHP
        const tourRows = [
            <?php foreach ($tours as $tour): ?>
            [
                \'#GT<?= str_pad($tour["id"], 3, "0", STR_PAD_LEFT) ?>\',
                \'<?= addslashes($tour["customerName"]) ?>\',
                \'<?= date("M d, Y", strtotime($tour["date"])) ?>\',
                \'<?= date("h:i A", strtotime($tour["time"])) ?>\',
                \'<?= addslashes($tour["location"]) ?>\',
                \'<?= addslashes($tour["language"]) ?>\',
                \'Rs. <?= number_format($tour["fee"], 2) ?>\'
            ],
            <?php endforeach; ?>
        ];

        // Total
        const totalFee = <?= array_sum(array_column($tours, "fee")) ?>;

        doc.autoTable({
            startY: y,
            head: [[\'Tour ID\', \'Customer\', \'Date\', \'Time\', \'Location\', \'Language\', \'Fee (LKR)\']],
            body: tourRows,
            foot: [[\'\', \'\', \'\', \'\', \'\', \'TOTAL REVENUE\', \'Rs. \' + totalFee.toLocaleString(\'en-US\', {minimumFractionDigits: 2})]],
            theme: \'grid\',
            headStyles: { fillColor: [44, 85, 48], textColor: 255, fontStyle: \'bold\', fontSize: 9, cellPadding: 3 },
            bodyStyles: { fontSize: 8.5, cellPadding: 2.5 },
            footStyles: { fillColor: [240, 248, 240], textColor: [44, 85, 48], fontStyle: \'bold\', fontSize: 10, cellPadding: 3 },
            columnStyles: {
                0: { cellWidth: 20 },
                1: { cellWidth: 40 },
                6: { halign: \'right\', fontStyle: \'bold\' }
            },
            margin: { left: 14, right: 14 }
        });

        // ---- FOOTER ----
        const totalPages = doc.internal.getNumberOfPages();
        const pageHeight = doc.internal.pageSize.height;
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(\'Ceylon Go Guide Performance Report\', 14, pageHeight - 10);
            doc.text(\'Page \' + i + \' of \' + totalPages, pageWidth - 14, pageHeight - 10, { align: \'right\' });
        }

        doc.save(\'guide_performance_report.pdf\');';
    $content = preg_replace($pdfPattern, $newPdfBody, $content);
    
    file_put_contents($guideFile, $content);
}
?>
