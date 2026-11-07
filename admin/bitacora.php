<?php
    include_once __DIR__ . '/models/bitacora/bitacora.class.php';
    $app = new bitacora();
    $app->checarRol('Administrador');
    $tituloPagina = 'Bitácora del sistema';
    $descripcionPagina = 'Consulta las actividades administrativas registradas en GORA.';
    $registros = $app->obtenerRegistros();
    include_once __DIR__ . '/../templates/admin/header.html';
    include_once __DIR__ . '/../templates/bitacora/index.html';
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
