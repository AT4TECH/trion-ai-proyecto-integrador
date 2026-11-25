<?php
// Pruebas reales de rangos de fechas de reportes de ventas (funcionalidad 002-reportes-fechas-validas).
// Rangos probados: invertido (rechazo), igual (permitido) y válido (permitido).
declare(strict_types=1);

require __DIR__ . '/../../../controllers/ReporteController.php';

$ok = true;
function registrar(bool $cond, string $nombre, string $detalle): void {
    global $ok;
    printf("[%s] %s%s\n", $cond ? 'PASS' : 'FAIL', $nombre, $cond ? '' : " -- $detalle");
    $ok = $ok && $cond;
}

// 1. Rango invertido con rechazarRangoInvertido=true: se rechaza, sin intercambiar fechas.
$repInv = new ReporteController(['desde' => '2026-12-01', 'hasta' => '2026-11-01'], true);
registrar($repInv->error !== '' && strpos($repInv->error, '2026-12-01') !== false, 'INVERTIDO(true): mensaje comprensible con las fechas', 'error=' . $repInv->error);
registrar($repInv->desde === '2026-12-01' && $repInv->hasta === '2026-11-01', 'INVERTIDO(true): sin intercambio de fechas', "desde={$repInv->desde} hasta={$repInv->hasta}");

// 2. Comportamiento original con el parámetro false: se conserva el intercambio.
$repInvF = new ReporteController(['desde' => '2026-12-01', 'hasta' => '2026-11-01']);
registrar($repInvF->error === '' && $repInvF->desde === '2026-11-01' && $repInvF->hasta === '2026-12-01', 'INVERTIDO(false): comportamiento original (intercambio)', "desde={$repInvF->desde} hasta={$repInvF->hasta}");

// 3. Rango igual: permitido, sin error.
$repIg = new ReporteController(['desde' => '2026-11-01', 'hasta' => '2026-11-01'], true);
registrar($repIg->error === '' && $repIg->desde === '2026-11-01' && $repIg->hasta === '2026-11-01', 'IGUAL: permitido y sin error', 'error=' . $repIg->error);
registrar(is_array($repIg->resumenVentas()), 'IGUAL: se generan resultados para el día', '');

// 4. Rango válido: permitido, sin error.
$repOk = new ReporteController(['desde' => '2026-10-01', 'hasta' => '2026-11-25'], true);
registrar($repOk->error === '' && $repOk->desde === '2026-10-01' && $repOk->hasta === '2026-11-25', 'VALIDO: sin error y rango conservado', 'error=' . $repOk->error);
registrar(is_array($repOk->resumenVentas()), 'VALIDO: se generan resultados del rango', '');

echo $ok ? "RESUMEN: PASS\n" : "RESUMEN: FAIL\n";
exit($ok ? 0 : 1);
