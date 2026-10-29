<?php
    include_once __DIR__ . '/models/compra/compra.class.php';
    $app = new compra();
    $app->checarPermiso('Administrar compras');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Compras';
    $descripcionPagina = 'Registra y consulta las compras realizadas.';
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if (isset($_POST['enviar'])) {
                $datos = $app->create($_POST['data']);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $_POST['data'];
                    include_once __DIR__ . '/../templates/compra/_form.html';
                } else {
                    $compras = $app->getall();
                    include_once __DIR__ . '/../templates/compra/index.html';
                }
            } else {
                include_once __DIR__ . '/../templates/compra/_form.html';
            }
            break;
        case 'update':
            if (isset($_POST['enviar'])) {
                $resultado = $app->update($id, $_POST['data']);
                if (is_array($resultado) && isset($resultado['errores'])) {
                    $errores = $resultado['errores'];
                    $datos = array_merge($_POST['data'], ['id_compra' => $id]);
                    include_once __DIR__ . '/../templates/compra/_form.html';
                } else {
                    $compras = $app->getall();
                    include_once __DIR__ . '/../templates/compra/index.html';
                }
            } else {
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/compra/_form.html';
            }
            break;
        case 'delete':
            $app->delete($id);
            $compras = $app->getall();
            include_once __DIR__ . '/../templates/compra/index.html';
            break;
        default:
            $compras = $app->getall();
            include_once __DIR__ . '/../templates/compra/index.html';
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
