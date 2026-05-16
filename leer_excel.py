import xlrd
import sys

path = r"C:\Users\Antonio\OneDrive\Escritorio\gespol\Cuadrantes Año 2026 SaaS.xls"

try:
    wb = xlrd.open_workbook(path, on_demand=True)
except Exception as e:
    # intentar con nombre alternativo
    import glob, os
    files = glob.glob(r"C:\Users\Antonio\OneDrive\Escritorio\gespol\Cuadrantes*.xls")
    if files:
        path = files[0]
        wb = xlrd.open_workbook(path, on_demand=True)
    else:
        print(f"ERROR: {e}")
        sys.exit(1)

print(f"Total hojas: {wb.nsheets}")
for i, name in enumerate(wb.sheet_names()):
    print(f"  Hoja {i}: {name}")

print("\n" + "="*60)

for idx in range(wb.nsheets):
    sheet = wb.sheet_by_index(idx)
    print(f"\n=== HOJA {idx}: '{sheet.name}' | {sheet.nrows} filas x {sheet.ncols} cols ===")
    
    max_rows = min(25, sheet.nrows)
    for r in range(max_rows):
        row_data = []
        for c in range(min(40, sheet.ncols)):
            val = sheet.cell_value(r, c)
            if val != '' and val != 0.0:
                row_data.append(f"[{c+1}]={val}")
        if row_data:
            print(f"  R{r+1}: {' | '.join(row_data)}")
    
    wb.unload_sheet(idx)

print("\n=== FIN ===")
