<?php
    include_once __DIR__ . '/models/venta/venta.class.php';
    $app = new venta();
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Ventas';
    $descripcionPagina = 'Registra y consulta las ventas realizadas.';
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if (isset($_POST['enviar'])) {
                $datos = $app->create($_POST['data']);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $_POST['data'];
                    include_once __DIR__ . '/../templates/venta/_form.html';
                } else {
                    $ventas = $app->getall();
                    include_once __DIR__ . '/../templates/venta/index.html';
                }
            } else {
                include_once __DIR__ . '/../templates/venta/_form.html';
            }
            break;
        case 'update':
            if (isset($_POST['enviar'])) {
                $resultado = $app->update($id, $_POST['data']);
                if (is_array($resultado) && isset($resultado['errores'])) {
                    $errores = $resultado['errores'];
                    $datos = array_merge($_POST['data'], ['id_venta' => $id]);
                    include_once __DIR__ . '/../templates/venta/_form.html';
                } else {
                    $ventas = $app->getall();
                    include_once __DIR__ . '/../templates/venta/index.html';
                }
            } else {
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/venta/_form.html';
            }
            break;
        case 'delete':
            $app->delete($id);
            $ventas = $app->getall();
            include_once __DIR__ . '/../templates/venta/index.html';
            break;
        default:
            $ventas = $app->getall();
            include_once __DIR__ . '/../templates/venta/index.html';
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
