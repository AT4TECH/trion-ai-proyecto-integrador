<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/permisos.php';
require_once __DIR__ . '/../../helpers/funciones.php';
require_once __DIR__ . '/../../controllers/ProductoController.php';
requirePermission('productos.ver');
$controller = new ProductoController();
$resumen = $controller->resumenStock();
$categorias = $controller->categorias();
$categoria = $_GET['categoria'] ?? '';
$categoria = is_string($categoria) && preg_match('/\A[1-9][0-9]*\z/', $categoria)
    ? filter_var($categoria, FILTER_VALIDATE_INT)
    : false;
$categoriaIds = array_map('intval', array_column($categorias, 'id_categoria'));
$categoriaSeleccionada = in_array($categoria, $categoriaIds, true) ? $categoria : null;
$productos = $controller->listar([
    'estado' => '1',
    'stock_bajo' => true,
    'categoria' => $categoriaSeleccionada,
]);
$titulo = 'Stock bajo';
require __DIR__ . '/../layouts/header.php';
?>
<div class="page-header"><h1>Stock bajo</h1><a class="btn btn-light" href="index.php">Ver todos los productos</a></div>
<h2 class="section-title">Resumen global</h2>
<div class="stats">
  <div class="stat"><span class="stat-label"><?= icono('productos') ?>Productos activos</span><strong class="stat-value"><?= (int)$resumen['activos'] ?></strong></div>
  <div class="stat"><span class="stat-label"><?= icono('inventario') ?>Con stock bajo</span><strong class="stat-value"><?= (int)$resumen['bajos'] ?></strong><span class="stat-detail">En o por debajo del stock mínimo</span></div>
  <div class="stat"><span class="stat-label"><?= icono('devoluciones') ?>Agotados</span><strong class="stat-value"><?= (int)$resumen['agotados'] ?></strong></div>
  <?php if (tienePermiso('productos.editar')): ?><div class="stat"><span class="stat-label"><?= icono('caja') ?>Inventario al costo</span><strong class="stat-value"><?= dinero($resumen['valor_costo']) ?></strong></div><?php endif; ?>
</div>
<h2 class="section-title">Productos por reabastecer</h2>
<form class="filtros card" method="get">
  <label>Categoría
    <select name="categoria">
      <option value=""<?= $categoriaSeleccionada === null ? ' selected' : '' ?>>Todas</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= (int)$c['id_categoria'] ?>"<?= $categoriaSeleccionada === (int)$c['id_categoria'] ? ' selected' : '' ?>><?= e($c['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button class="btn">Filtrar</button>
</form>
<div class="table-card">
<table class="table">
  <thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th class="num">Stock</th><th class="num">Mínimo</th><th class="num">Faltan</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($productos as $p): ?>
    <tr>
      <td><?= e($p['codigo']) ?></td><td><strong><?= e($p['nombre']) ?></strong></td><td><?= e($p['categoria']) ?></td>
      <td class="num"><span class="badge <?= (float)$p['stock'] <= 0 ? 'badge-danger' : 'badge-warn' ?>"><?= cantidad($p['stock']) ?> <?= e($p['unidad_medida']) ?></span></td>
      <td class="num"><?= cantidad($p['stock_minimo']) ?></td>
      <td class="num"><?= cantidad(max((float)$p['stock_minimo'] - (float)$p['stock'], 0)) ?></td>
      <td class="acciones"><?php if (tienePermiso('productos.editar')): ?><a class="btn btn-light btn-sm" href="editar.php?id=<?= (int)$p['id_producto'] ?>">Editar</a><?php endif; ?></td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$productos): ?>
    <tr><td colspan="7" class="empty"><?= $categoriaSeleccionada === null
        ? 'Todos los productos activos tienen stock suficiente.'
        : 'No hay productos activos con stock bajo en esta categoría.' ?></td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
