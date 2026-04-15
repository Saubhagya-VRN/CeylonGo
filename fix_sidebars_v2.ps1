$transportDir = "c:\xampp\htdocs\CeylonGo\views\transport"
$guideDir = "c:\xampp\htdocs\CeylonGo\views\guide"

# Fix Transport Sidebars
Get-ChildItem -Path $transportDir -Filter "*.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    # Swap Report and Payment
    if ($content -match 'report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>\s*<li><a href="/CeylonGo/public/transporter/payment') {
        $content = $content -replace '(<li><a href="/CeylonGo/public/transporter/report">.*?</li>)\s*(<li><a href="/CeylonGo/public/transporter/payment">.*?</li>)', '$2`n        $1'
        Set-Content -Path $_.FullName -Value $content -NoNewline
        Write-Host "Updated Transport Sidebar: $($_.Name)"
    }
}

# Fix Guide Sidebars
Get-ChildItem -Path $guideDir -Filter "*.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    
    # First Remove My Places if it exists
    if ($content -match 'guide/places') {
        $content = $content -replace '\s*<li><a href="/CeylonGo/public/guide/places">.*?</li>', ''
    }
    
    # Swap Report and Payment
    # The structure might be: Report -> (Places) -> Payment
    # After removing Places, it should be Report -> Payment
    if ($content -match 'report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>\s*<li><a href="/CeylonGo/public/guide/payment') {
        $content = $content -replace '(<li><a href="/CeylonGo/public/guide/report">.*?</li>)\s*(<li><a href="/CeylonGo/public/guide/payment">.*?</li>)', '$2`n        $1'
    }
    
    Set-Content -Path $_.FullName -Value $content -NoNewline
    Write-Host "Updated Guide Sidebar: $($_.Name)"
}
