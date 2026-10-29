<?php
    include_once __DIR__ . '/models/material/material.class.php';
    $app = new material();
    $app->checarPermiso('Administrar materiales');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Materiales';
    $descripcionPagina = 'Administra el catálogo de materiales.';
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if (isset($_POST['enviar'])) {
                $datos = $app->create($_POST['data']);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $_POST['data'];
                    include_once __DIR__ . '/../templates/material/_form.html';
                } else {
                    $materiales = $app->getall();
                    include_once __DIR__ . '/../templates/material/index.html';
                }
            } else {
                include_once __DIR__ . '/../templates/material/_form.html';
            }
            break;
        case 'update':
            if (isset($_POST['enviar'])) {
                $datos = $app->update($id, $_POST['data']);
                if (is_array($datos) && isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = array_merge($_POST['data'], ['id_material' => $id]);
                    include_once __DIR__ . '/../templates/material/_form.html';
                } else {
                    $materiales = $app->getall();
                    include_once __DIR__ . '/../templates/material/index.html';
                }
            } else {
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/material/_form.html';
            }
            break;
        case 'delete':
            $app->delete($id);
            $materiales = $app->getall();
            include_once __DIR__ . '/../templates/material/index.html';
            break;
        default:
            $materiales = $app->getall();
            include_once __DIR__ . '/../templates/material/index.html';
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
