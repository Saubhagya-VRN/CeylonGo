$filePath = "c:\xampp\htdocs\CeylonGo\views\tourist\tourist_dashboard.php"
$lines = Get-Content $filePath

# Find and remove the small tag with service hours
$newLines = @()
$skipNext = 0

for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($skipNext -gt 0) {
        $skipNext--
        continue
    }
    
    if ($lines[$i] -match 'small style.*color: #666') {
        # Skip this line and the next 2 lines (the text and closing tag)
        $skipNext = 2
        continue
    }
    
    $newLines += $lines[$i]
}

# Write back to file
$newLines | Set-Content $filePath

Write-Host "Service hours text removed successfully!"
Write-Host "Removed from line around $(1639)"
