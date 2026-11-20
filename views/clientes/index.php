<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/permisos.php';
require_once __DIR__ . '/../../helpers/funciones.php';
require_once __DIR__ . '/../../controllers/ClienteController.php';
requirePermission('clientes.ver');
$controller = new ClienteController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePermission('clientes.eliminar');
    verificarCsrf();
    $eliminar = ($_POST['accion'] ?? '') === 'eliminar';
    $error = $eliminar ? $controller->eliminar((int)($_POST['id'] ?? 0)) : $controller->cambiarEstado((int)($_POST['id'] ?? 0));
    $error ? flash('error', $error) : flash('success', $eliminar ? 'Cliente eliminado.' : 'Estado del cliente actualizado.');
    redirect('index.php');
}
$filtroTexto = static function (string $clave): string {
    $valor = $_GET[$clave] ?? '';
    return is_string($valor) ? mb_substr(trim($valor), 0, 100) : '';
};
$estado = $_GET['estado'] ?? '1';
$filtros = [
    'q' => $filtroTexto('q'),
    'estado' => in_array($estado, ['1', '0', ''], true) ? $estado : '1',
    'ciudad' => $filtroTexto('ciudad'),
];
$pagina = $_GET['pagina'] ?? '';
$pagina = is_string($pagina) && preg_match('/\A[0-9]+\z/', $pagina) ? max(1, (int)$pagina) : 1;
$resultado = $controller->pagina($filtros, $pagina);
$clientes = $resultado['registros'];
$enlacePagina = static fn(int $numero): string => '?' . http_build_query($filtros + ['pagina' => $numero]);
$titulo = 'Clientes';
require __DIR__ . '/../layouts/header.php';
?>
<div class="page-header">
  <h1>Clientes</h1>
  <?php if (tienePermiso('clientes.crear')): ?><a class="btn" href="crear.php">+ Nuevo cliente</a><?php endif; ?>
</div>
<form class="filtros card" method="get" action="index.php">
  <label class="grow">Buscar<input type="search" name="q" value="<?= e($filtros['q']) ?>" placeholder="Nombre, documento, teléfono o correo" autofocus></label>
  <label>Ciudad<input type="text" name="ciudad" value="<?= e($filtros['ciudad']) ?>" maxlength="100"></label>
  <label>Estado
    <select name="estado"><?php foreach (['1' => 'Activos', '0' => 'Inactivos', '' => 'Todos'] as $k => $v): ?><option value="<?= $k ?>" <?= $filtros['estado'] === (string)$k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
  </label>
  <button class="btn">Filtrar</button>
</form>
<p class="resumen-filtro"><?= $resultado['total'] ?> clientes</p>
<div class="table-card">
<table class="table">
  <thead><tr><th>Cliente</th><th>Documento</th><th>Contacto</th><th>Ciudad</th><th class="num">Compras</th><th>Última compra</th><th>Estado</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($clientes as $c): $general = $controller->esGeneral($c); ?>
    <tr class="<?= $c['estado'] ? '' : 'inactivo' ?>">
      <td><a href="editar.php?id=<?= (int)$c['id_cliente'] ?>"><?= e($c['nombre']) ?></a><?= $general ? ' <span class="badge">General</span>' : '' ?></td>
      <td><?= e($c['documento'] ?? '—') ?></td>
      <td><?= e($c['telefono'] ?? '') ?><?= $c['email'] ? '<br><small class="muted">' . e($c['email']) . '</small>' : '' ?><?= !$c['telefono'] && !$c['email'] ? '—' : '' ?></td>
      <td><?= e($c['ciudad'] ?? '—') ?></td>
      <td class="num"><?= (int)$c['compras'] ?><?= $c['compras'] ? '<br><small class="muted">' . dinero($c['total_compras']) . '</small>' : '' ?></td>
      <td><?= $c['ultima_compra'] ? date('d/m/Y', strtotime($c['ultima_compra'])) : '—' ?></td>
      <td><span class="badge <?= $c['estado'] ? 'badge-ok' : 'badge-off' ?>"><?= $c['estado'] ? 'Activo' : 'Inactivo' ?></span></td>
      <td class="acciones">
        <a class="btn btn-light btn-sm" href="editar.php?id=<?= (int)$c['id_cliente'] ?>"><?= tienePermiso('clientes.editar') ? 'Editar' : 'Ver' ?></a>
        <?php if (tienePermiso('clientes.eliminar') && !$general): ?>
          <form method="post" onsubmit="return confirm('¿<?= $c['estado'] ? 'Desactivar' : 'Activar' ?> este cliente?')">
            <?= csrfCampo() ?><input type="hidden" name="id" value="<?= (int)$c['id_cliente'] ?>">
            <button class="btn btn-sm <?= $c['estado'] ? 'btn-danger' : 'btn-light' ?>"><?= $c['estado'] ? 'Desactivar' : 'Activar' ?></button>
          </form>
          <?= botonEliminar('index.php', (int)$c['id_cliente'], $c['nombre']) ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$clientes): ?><tr><td colspan="8" class="empty">No hay clientes con estos filtros.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>
<?php if ($resultado['paginas'] > 1): ?>
  <nav class="paginacion" aria-label="Paginación de clientes">
    <?php if ($resultado['pagina'] > 1): ?>
      <a class="btn btn-light btn-sm" href="<?= e($enlacePagina($resultado['pagina'] - 1)) ?>">← Anterior</a>
    <?php endif; ?>
    <span>Página <?= $resultado['pagina'] ?> de <?= $resultado['paginas'] ?></span>
    <?php if ($resultado['pagina'] < $resultado['paginas']): ?>
      <a class="btn btn-light btn-sm" href="<?= e($enlacePagina($resultado['pagina'] + 1)) ?>">Siguiente →</a>
    <?php endif; ?>
  </nav>
<?php endif; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
