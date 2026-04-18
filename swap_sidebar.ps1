$dirs = @("c:\xampp\htdocs\CeylonGo\views\guide", "c:\xampp\htdocs\CeylonGo\views\transport")
foreach ($dir in $dirs) {
    $files = Get-ChildItem -Path $dir -Filter "*.php"
    foreach ($file in $files) {
        $lines = Get-Content $file.FullName
        $newLines = @()
        $reportLine = $null
        for ($i = 0; $i -lt $lines.Count; $i++) {
            if ($lines[$i] -match 'Performance Report</a></li>') {
                $reportLine = $lines[$i]
                continue
            }
            if ($reportLine -ne $null -and $lines[$i] -match 'My Payment</a></li>') {
                $newLines += $lines[$i]
                $newLines += $reportLine
                $reportLine = $null
                continue
            }
            if ($reportLine -ne $null) {
                $newLines += $reportLine
                $reportLine = $null
            }
            $newLines += $lines[$i]
        }
        if ($reportLine -ne $null) { $newLines += $reportLine }
        $newLines | Set-Content -Path $file.FullName
        Write-Host "Done: $($file.Name)"
    }
}
