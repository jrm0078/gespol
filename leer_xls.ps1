$excel = New-Object -ComObject Excel.Application
$excel.Visible = $false
$excel.DisplayAlerts = $false

$wb = $excel.Workbooks.Open("C:\Users\Antonio\OneDrive\Escritorio\gespol\Cuadrantes Año 2026 SaaS.xls", 0, $true)

Write-Host "Hojas encontradas:"
foreach ($sheet in $wb.Worksheets) {
    Write-Host "  - $($sheet.Name)"
}

foreach ($sheet in $wb.Worksheets) {
    Write-Host ""
    Write-Host "=== HOJA: $($sheet.Name) ==="
    $usedRange = $sheet.UsedRange
    $rows = $usedRange.Rows.Count
    $cols = $usedRange.Columns.Count
    Write-Host "  Dimensiones: $rows filas x $cols columnas"
    
    $maxRow = [Math]::Min(15, $rows)
    for ($r = 1; $r -le $maxRow; $r++) {
        $rowData = New-Object System.Collections.ArrayList
        for ($c = 1; $c -le [Math]::Min($cols, 40); $c++) {
            $cell = $usedRange.Cells.Item($r, $c)
            $val = $cell.Text
            if ($val -ne '') { 
                $null = $rowData.Add("[$c]=$val")
            }
        }
        if ($rowData.Count -gt 0) { 
            Write-Host "  Fila$r : $($rowData -join ' | ')"
        }
    }
}

$wb.Close($false)
$excel.Quit()
[System.Runtime.InteropServices.Marshal]::ReleaseComObject($excel) | Out-Null
