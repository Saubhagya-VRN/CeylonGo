$filePath = "c:\xampp\htdocs\CeylonGo\views\tourist\tourist_dashboard.php"
$content = Get-Content $filePath -Raw

# Remove the service hours text
$pattern = '              <small style="color: #666; font-size: 0.85em; display: block; margin-top: 5px;">' + "`r`n" + '                Service hours: 5:00 AM - 11:00 PM \| Book at least 2 hours in advance' + "`r`n" + '              </small>' + "`r`n"

$content = $content -replace [regex]::Escape($pattern), ''

# Also try without carriage returns in case
$pattern2 = '              <small style="color: #666; font-size: 0.85em; display: block; margin-top: 5px;">' + "`n" + '                Service hours: 5:00 AM - 11:00 PM \| Book at least 2 hours in advance' + "`n" + '              </small>' + "`n"

$content = $content -replace [regex]::Escape($pattern2), ''

Set-Content $filePath -Value $content -NoNewline

Write-Host "Service hours text removed successfully!"
