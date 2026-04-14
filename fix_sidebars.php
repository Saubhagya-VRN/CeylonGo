<?php
$dirs = ["c:\\xampp\\htdocs\\CeylonGo\\views\\guide", "c:\\xampp\\htdocs\\CeylonGo\\views\\transport"];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') continue;
        
        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);
        $original = $content;
        $isGuide = strpos($dir, 'guide') !== false;

        // Cleanup previous failed attempts
        $content = str_replace('`n', "\n", $content);

        // Remove My Places from Guide
        if ($isGuide) {
            $content = preg_replace('/\s*<li><a href="\/CeylonGo\/public\/guide\/places">.*?<\/li>/s', '', $content);
        }

        // Reorder sidebar: Performance Report to bottom
        // Regex to find <li> for report and payment even if they have classes
        $reportPattern = '(\s*<li[^>]*><a href="[^"]*report">.*?<\/li>)';
        $paymentPattern = '(\s*<li[^>]*><a href="[^"]*payment">.*?<\/li>)';
        
        // Match sequence of report then payment (with any whitespace between)
        if (preg_match("/$reportPattern(\s*)$paymentPattern/s", $content, $matches)) {
            $fullMatch = $matches[0];
            $reportLi = $matches[1];
            $spacing = $matches[2];
            $paymentLi = $matches[3];
            
            // Reconstruct: payment first, then report
            $newOrder = $paymentLi . $spacing . $reportLi;
            $content = str_replace($fullMatch, $newOrder, $content);
        }

        if ($content !== $original) {
            file_put_contents($filePath, $content);
            echo "Fixed: $filePath\n";
        }
    }
}
echo "Sidebar cleanup complete.\n";
