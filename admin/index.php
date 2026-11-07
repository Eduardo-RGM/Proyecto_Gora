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
    $eventosCalendario = [];
    foreach ($venta->getall() as $registro) {
        $eventosCalendario[] = [
            'title' => 'Venta: ' . $registro['material'],
            'start' => $registro['fecha'],
            'color' => '#176b87'
        ];
    }
    foreach ($compra->getall() as $registro) {
        $eventosCalendario[] = [
            'title' => 'Compra: ' . $registro['material'],
            'start' => $registro['fecha'],
            'color' => '#79b85b'
        ];
    }
    include_once __DIR__ . '/../templates/admin/index.html';
?>