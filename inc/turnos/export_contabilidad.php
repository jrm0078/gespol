<?php
/**
 * inc/turnos/export_contabilidad.php
 * Genera un archivo Excel (.xlsx) con la contabilidad mensual.
 * Llamar vía GET: ?ejercicio=2026&mes=5
 *
 * Seguridad: requiere sesión válida (incluye seguridad.php).
 */
ob_start();
include_once '../config.inc.php';
include_once '../genericasPHP.php';
include_once '../seguridad.php';
include_once 'func_turnos.php';
ob_end_clean();

$ejercicio = intval($_GET['ejercicio'] ?? 0);
$mes       = intval($_GET['mes']       ?? 0);

if (!$ejercicio || $mes < 1 || $mes > 12) {
    http_response_code(400);
    exit('Parámetros no válidos');
}

// Cargar datos usando la función existente
$json = turnos_cargarContabilidad($ejercicio, $mes);
$data = json_decode($json, true);

if (!$data || $data['validacion'] !== 'ok') {
    http_response_code(404);
    exit($data['mensaje'] ?? 'Error al cargar datos');
}

$filas         = $data['filas']        ?? [];
$cuadrante     = $data['cuadrante']    ?? [];
$dias_teoricos = intval($data['dias_teoricos'] ?? 0);

$meses_nombre = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

$titulo_hoja  = 'Contabilidad ' . $meses_nombre[$mes] . ' ' . $ejercicio;
$nombre_archivo = 'contabilidad_' . $ejercicio . '_' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '.xlsx';

// Cargar PHPExcel
require_once dirname(__FILE__) . '/../../libs/PHPExcel-1.8/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

// Propiedades del documento
$objPHPExcel->getProperties()
    ->setCreator('GESPOL - Gestión de Turnos')
    ->setLastModifiedBy('GESPOL')
    ->setTitle($titulo_hoja)
    ->setSubject('Contabilidad mensual de turnos')
    ->setDescription('Exportación generada automáticamente desde GESPOL');

$sheet = $objPHPExcel->getActiveSheet();
$sheet->setTitle(substr($titulo_hoja, 0, 31)); // Excel limita a 31 chars

// ── Estilos reutilizables ────────────────────────────────────
$estilo_titulo = [
    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => '0066B3']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER],
];
$estilo_cabecera = [
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => '004A8F']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'wrap'       => true],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,
                                     'color' => ['rgb' => 'B0C4DE']]],
];
$estilo_cabecera_manual = [
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => '7A4400']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'wrap'       => true],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,
                                     'color' => ['rgb' => 'D4A070']]],
];
$estilo_dato_auto = [
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => 'EEF6FF']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,
                                     'color' => ['rgb' => 'C8DCEE']]],
];
$estilo_dato_manual = [
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => 'FFF8EE']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,
                                     'color' => ['rgb' => 'DDBB99']]],
];
$estilo_equipo = [
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '0066B3']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => 'E0EFFF']],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,
                                     'color' => ['rgb' => '99BBDD']]],
];
$estilo_total = [
    'font'      => ['bold' => true, 'size' => 9],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => 'E8F5E9']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                                     'color' => ['rgb' => '28A745']]],
];

// ── Fila 1: Título ───────────────────────────────────────────
$sheet->mergeCells('A1:Q1');
$sheet->setCellValue('A1', $titulo_hoja .
    '  |  Estado: ' . strtoupper($cuadrante['estado'] ?? '–') .
    '  |  Días teóricos del mes: ' . $dias_teoricos);
$sheet->getStyle('A1')->applyFromArray($estilo_titulo);
$sheet->getRowDimension(1)->setRowHeight(22);

// ── Fila 2: Cabeceras ────────────────────────────────────────
$row = 2;
$cols = [
    'A' => 'Agente',
    'B' => 'Equipo',
    'C' => 'Jornadas',
    'D' => 'Dif.',
    'E' => 'Fest.\ntrab.',
    'F' => 'F/S\ntrab.',
    'G' => 'Vac.',
    'H' => 'Baja',
    'I' => 'Perm.',
    'J' => 'Form.',
    'K' => 'H.Red.',
    'L' => 'P01',
    'M' => 'P040',
    'N' => 'Ext.h',
    'O' => 'Desc.',
    'P' => 'Ajuste',
    'Q' => 'Obs.',
];
foreach ($cols as $col => $cab) {
    $sheet->setCellValue($col . $row, $cab);
    if ($col <= 'M') {
        $sheet->getStyle($col . $row)->applyFromArray($estilo_cabecera);
    } else {
        $sheet->getStyle($col . $row)->applyFromArray($estilo_cabecera_manual);
    }
}
$sheet->getRowDimension($row)->setRowHeight(28);

// ── Datos ────────────────────────────────────────────────────
$row++;
$equipoActual = null;

// Para totales
$tot = array_fill_keys(['jorn','fest','fs','vac','baja','perm','form','hred','p01','p040','ext','desc','ajus'], 0);
$primera_fila_datos = $row;

foreach ($filas as $f) {
    $grupoKey = ($f['equipo_codigo'] ?? '') . '|' . ($f['equipo_nombre'] ?? '');
    if ($grupoKey !== $equipoActual) {
        $equipoActual = $grupoKey;
        $sheet->mergeCells('A' . $row . ':Q' . $row);
        $sheet->setCellValue('A' . $row,
            ($f['equipo_codigo'] ?? '–') . ' – ' . ($f['equipo_nombre'] ?? '–'));
        $sheet->getStyle('A' . $row . ':Q' . $row)->applyFromArray($estilo_equipo);
        $row++;
    }

    $jorn = floatval($f['jornadas_mes']            ?? 0);
    $fest = intval($f['festivos_trabajados']        ?? 0);
    $fs   = intval($f['fines_semana_trabajados']    ?? 0);
    $vac  = intval($f['vacaciones_dias']            ?? 0);
    $baja = intval($f['bajas_dias']                 ?? 0);
    $perm = intval($f['permisos_dias']              ?? 0);
    $form = intval($f['formacion_dias']             ?? 0);
    $hred = floatval($f['horas_reduccion']          ?? 0);
    $p01  = floatval($f['p01_jornadas']             ?? 0);
    $p040 = floatval($f['p040_jornadas']            ?? 0);
    $ext  = floatval($f['extras_horas']             ?? 0);
    $desc = floatval($f['descuentos']               ?? 0);
    $ajus = floatval($f['ajuste_manual']            ?? 0);
    $obs  = $f['observaciones']                     ?? '';
    $dif  = $dias_teoricos > 0 ? ($jorn - $dias_teoricos) : 0;

    $sheet->setCellValue('A' . $row, $f['nombre_completo'] ?? ($f['agente_nombre'] ?? ''));
    $sheet->setCellValue('B' . $row, ($f['equipo_codigo'] ?? ''));
    $sheet->setCellValue('C' . $row, $jorn);
    $sheet->setCellValue('D' . $row, $dias_teoricos > 0 ? $dif : '');
    $sheet->setCellValue('E' . $row, $fest);
    $sheet->setCellValue('F' . $row, $fs);
    $sheet->setCellValue('G' . $row, $vac);
    $sheet->setCellValue('H' . $row, $baja);
    $sheet->setCellValue('I' . $row, $perm);
    $sheet->setCellValue('J' . $row, $form);
    $sheet->setCellValue('K' . $row, $hred);
    $sheet->setCellValue('L' . $row, $p01);
    $sheet->setCellValue('M' . $row, $p040);
    $sheet->setCellValue('N' . $row, $ext  ?: '');
    $sheet->setCellValue('O' . $row, $desc ?: '');
    $sheet->setCellValue('P' . $row, $ajus ?: '');
    $sheet->setCellValue('Q' . $row, $obs);

    // Aplicar estilos por columna
    foreach (['C','D','E','F','G','H','I','J','K','L','M'] as $c) {
        $sheet->getStyle($c . $row)->applyFromArray($estilo_dato_auto);
    }
    foreach (['N','O','P','Q'] as $c) {
        $sheet->getStyle($c . $row)->applyFromArray($estilo_dato_manual);
    }
    // Estilo nombre agente
    $sheet->getStyle('A' . $row)->applyFromArray([
        'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,
                                       'color' => ['rgb' => 'AAAAAA']]],
    ]);

    // Colorear diferencia
    if ($dias_teoricos > 0) {
        if ($dif > 0)      $sheet->getStyle('D' . $row)->getFont()->getColor()->setRGB('28A745');
        elseif ($dif < 0)  $sheet->getStyle('D' . $row)->getFont()->getColor()->setRGB('DC3545');
    }

    // Acumular totales
    $tot['jorn'] += $jorn; $tot['fest'] += $fest; $tot['fs']   += $fs;
    $tot['vac']  += $vac;  $tot['baja'] += $baja; $tot['perm'] += $perm;
    $tot['form'] += $form; $tot['hred'] += $hred; $tot['p01']  += $p01;
    $tot['p040'] += $p040; $tot['ext']  += $ext;  $tot['desc'] += $desc;
    $tot['ajus'] += $ajus;

    $row++;
}

// ── Fila de totales ──────────────────────────────────────────
$sheet->setCellValue('A' . $row, 'TOTALES');
$sheet->setCellValue('B' . $row, '');
$sheet->setCellValue('C' . $row, round($tot['jorn'], 2));
$sheet->setCellValue('D' . $row, '');
$sheet->setCellValue('E' . $row, $tot['fest']);
$sheet->setCellValue('F' . $row, $tot['fs']);
$sheet->setCellValue('G' . $row, $tot['vac']);
$sheet->setCellValue('H' . $row, $tot['baja']);
$sheet->setCellValue('I' . $row, $tot['perm']);
$sheet->setCellValue('J' . $row, $tot['form']);
$sheet->setCellValue('K' . $row, round($tot['hred'], 2));
$sheet->setCellValue('L' . $row, $tot['p01']);
$sheet->setCellValue('M' . $row, $tot['p040']);
$sheet->setCellValue('N' . $row, $tot['ext']  ?: '');
$sheet->setCellValue('O' . $row, $tot['desc'] ?: '');
$sheet->setCellValue('P' . $row, $tot['ajus'] ?: '');
$sheet->setCellValue('Q' . $row, '');
$sheet->getStyle('A' . $row . ':Q' . $row)->applyFromArray($estilo_total);

// ── Anchos de columnas ───────────────────────────────────────
$sheet->getColumnDimension('A')->setWidth(28);
$sheet->getColumnDimension('B')->setWidth(8);
foreach (['C','D','E','F','G','H','I','J','K','L','M','N','O','P'] as $c) {
    $sheet->getColumnDimension($c)->setWidth(7);
}
$sheet->getColumnDimension('Q')->setWidth(30);

// Fijar fila de cabecera al hacer scroll
$sheet->freezePane('A3');

// ── Cabecera HTTP y salida ───────────────────────────────────
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->save('php://output');
exit;
