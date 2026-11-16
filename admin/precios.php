<?php
    include_once __DIR__ . '/models/precio/precio.class.php';
    $app = new precio();
    $app->checarPermiso('Administrar materiales');
    $tituloPagina = 'Precios de compra';
    $descripcionPagina = 'Actualiza los precios vigentes de cada material.';
    $errores = [];
    $mensaje = '';

    if (isset($_POST['guardar'])) {
        $errores = $app->actualizar($_POST['precio'] ?? []);
        if (!$errores) {
            $mensaje = 'Los precios se actualizaron correctamente.';
        }
    }

    $precios = $app->getall();
    include_once __DIR__ . '/../templates/admin/header.html';
    include_once __DIR__ . '/../templates/precio/index.html';
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
