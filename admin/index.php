<?php
    include_once __DIR__ . '/models/sistema.class.php';
    include_once __DIR__ . '/models/venta/venta.class.php';
    include_once __DIR__ . '/models/compra/compra.class.php';
    $app = new sistema();
    $app->checarSesion();
    $venta = new venta();
    $compra = new compra();
    $ventasGrafica = $venta->getResumenPorMes();
    $comprasGrafica = $compra->getResumenPorMes();
    include_once __DIR__ . '/../templates/admin/index.html';
?>